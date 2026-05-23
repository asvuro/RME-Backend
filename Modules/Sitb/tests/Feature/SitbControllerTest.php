<?php

namespace Modules\Sitb\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Auth\Models\User;
use Modules\Sitb\Models\PasienTb;
use Tests\TestCase;

class SitbControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_creates_a_pasien_tb_row_and_sends_it_with_transformed_fields(): void
    {
        Http::fake(['*' => Http::response(['status' => 'berhasil', 'id_tb_03' => 'TB03-1'])]);

        $this->actingUser();

        $response = $this->postJson('/api/v1/sitb/pasien-tb', [
            'nourut_pasien' => '1234567890',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 1,
            'tgl_lahir' => '1990-05-17',
            'klasifikasi_lokasi_anatomi' => 0,
        ]);

        $response->assertCreated();
        $this->assertSame(0, $response->json('kirim'));
        $this->assertSame('TB03-1', $response->json('id_tb_03'));

        Http::assertSent(function ($request) {
            return $request['jenis_kelamin'] === 'L'
                && $request['tgl_lahir'] === '19900517'
                && ! array_key_exists('klasifikasi_lokasi_anatomi', $request->data());
        });
    }

    public function test_it_keeps_kirim_flag_set_when_sitb_rejects(): void
    {
        Http::fake(['*' => Http::response(['status' => 'gagal', 'keterangan' => 'NIK invalid'])]);

        $this->actingUser();
        $pasienTb = PasienTb::factory()->create();

        $response = $this->putJson("/api/v1/sitb/pasien-tb/{$pasienTb->id}", [
            'nourut_pasien' => $pasienTb->nourut_pasien,
        ]);

        $response->assertOk();
        $this->assertSame(1, $response->json('kirim'));
        $this->assertSame('NIK invalid', $response->json('error_message'));
    }

    public function test_store_ignores_internal_columns_forced_in_the_payload(): void
    {
        // Rejection response: the service must stay the only writer of
        // error_message/id_tb_03, and kirim must survive as 1 (retry queue).
        Http::fake(['*' => Http::response(['status' => 'gagal', 'keterangan' => 'NIK invalid'])]);

        $user = $this->actingUser();

        $response = $this->postJson('/api/v1/sitb/pasien-tb', [
            'nik' => '3201010101010001',
            'jenis_kelamin' => 2,
            'tgl_lahir' => '1985-03-10',
            // Mass-assignment attempt: forge integrity markers + actor.
            'kirim' => 0,
            'final' => 1,
            'error_message' => 'hack',
            'oleh' => 999,
            'id_tb_03' => 'SPOOFED',
            'unknown_field' => 'ignored',
        ]);

        $response->assertCreated();

        $row = PasienTb::where('nik', '3201010101010001')->sole();
        $this->assertSame(1, $row->kirim); // queued, not the forced 0
        $this->assertSame($user->id, $row->oleh); // actor stamped server-side
        $this->assertNull($row->final);
        $this->assertSame('NIK invalid', $row->error_message); // service-set, not 'hack'
        $this->assertNull($row->id_tb_03); // SITB-assigned only
    }

    public function test_update_ignores_internal_columns_forced_in_the_payload(): void
    {
        Http::fake(['*' => Http::response(['status' => 'gagal', 'keterangan' => 'NIK invalid'])]);

        $this->actingUser();
        $pasienTb = PasienTb::factory()->create(['oleh' => 777]);

        $response = $this->putJson("/api/v1/sitb/pasien-tb/{$pasienTb->id}", [
            'alamat_lengkap' => 'Jl. Mawar No. 1',
            'kirim' => 0,
            'final' => 1,
            'error_message' => 'hack',
            'oleh' => 999,
            'id_tb_03' => 'SPOOFED',
        ]);

        $response->assertOk();

        $row = $pasienTb->fresh();
        $this->assertSame('Jl. Mawar No. 1', $row->alamat_lengkap); // real field applied
        $this->assertSame(1, $row->kirim); // update always re-queues, forced 0 ignored
        $this->assertSame(777, $row->oleh); // forced 999 ignored, original kept
        $this->assertNull($row->final);
        $this->assertSame('NIK invalid', $row->error_message);
    }

    public function test_store_rejects_type_violations_on_legitimate_columns(): void
    {
        Http::fake();

        $this->actingUser();

        $this->postJson('/api/v1/sitb/pasien-tb', [
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'bukan-angka',
        ])->assertStatus(422);

        $this->postJson('/api/v1/sitb/pasien-tb', [
            'tgl_lahir' => '17/05/1990',
        ])->assertStatus(422);

        $this->assertDatabaseCount('pasien_tb', 0);
    }
}
