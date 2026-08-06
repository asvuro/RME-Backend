<?php

namespace Modules\PendaftaranSelfCheckin\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\PendaftaranSelfCheckin\Models\SelfCheckinQueue;
use Tests\TestCase;

class SelfCheckinIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_index_filters_by_ward_and_date(): void
    {
        $this->actingUser();
        $wardA = Ward::factory()->create();
        $wardB = Ward::factory()->create();

        SelfCheckinQueue::factory()->count(2)->create(['ward_id' => $wardA->id]);
        SelfCheckinQueue::factory()->create(['ward_id' => $wardB->id]);
        // Kemarin di ward yang sama tidak boleh ikut terlist.
        SelfCheckinQueue::factory()->create([
            'ward_id' => $wardA->id,
            'checked_in_at' => now()->subDay(),
            'queue_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->getJson(
            '/api/v1/self-checkin-queues?ward_id='.$wardA->id.'&date='.now()->toDateString(),
        );

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_defaults_to_today_when_date_is_omitted(): void
    {
        $this->actingUser();

        SelfCheckinQueue::factory()->create();
        SelfCheckinQueue::factory()->create([
            'checked_in_at' => now()->subDay(),
            'queue_date' => now()->subDay()->toDateString(),
        ]);

        // Layar kiosk tidak kirim tanggal: default hari ini.
        $this->getJson('/api/v1/self-checkin-queues')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
