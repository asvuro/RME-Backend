<?php

namespace Modules\GeneralSitbTb03RoTransfer\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralSitbTb03RoTransfer\Models\SitbTb03RoTransfer;
use Tests\TestCase;

class SitbTb03RoTransferControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_sitb_tb03_ro_transfer(): void
    {
        $this->actingUser();
        SitbTb03RoTransfer::factory()->count(3)->create();

        $this->getJson('/api/v1/sitb-tb03-ro-transfers')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_sitb_tb03_ro_transfer(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/sitb-tb03-ro-transfers', ['name' => 'Contoh Sitbpindahtb03ro', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Sitbpindahtb03ro');

        $this->assertDatabaseHas('sitb_tb03_ro_transfers', ['name' => 'Contoh Sitbpindahtb03ro']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        SitbTb03RoTransfer::factory()->create(['name' => 'Contoh Sitbpindahtb03ro']);

        $this->postJson('/api/v1/sitb-tb03-ro-transfers', ['name' => 'Contoh Sitbpindahtb03ro'])->assertStatus(422);
    }

    public function test_it_deletes_sitb_tb03_ro_transfer(): void
    {
        $this->actingUser();
        $sitbTb03RoTransfer = SitbTb03RoTransfer::factory()->create();

        $this->deleteJson("/api/v1/sitb-tb03-ro-transfers/{$sitbTb03RoTransfer->id}")->assertStatus(204);
        $this->assertDatabaseMissing('sitb_tb03_ro_transfers', ['id' => $sitbTb03RoTransfer->id]);
    }
}