<?php

namespace Modules\GeneralMonthName\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMonthName\Models\MonthName;
use Tests\TestCase;

class MonthNameControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_month_name(): void
    {
        $this->actingUser();
        MonthName::factory()->count(3)->create();

        $this->getJson('/api/v1/month-names')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_month_name(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/month-names', ['name' => 'Contoh Namabulan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Namabulan');

        $this->assertDatabaseHas('month_names', ['name' => 'Contoh Namabulan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MonthName::factory()->create(['name' => 'Contoh Namabulan']);

        $this->postJson('/api/v1/month-names', ['name' => 'Contoh Namabulan'])->assertStatus(422);
    }

    public function test_it_deletes_month_name(): void
    {
        $this->actingUser();
        $monthName = MonthName::factory()->create();

        $this->deleteJson("/api/v1/month-names/{$monthName->id}")->assertStatus(204);
        $this->assertDatabaseMissing('month_names', ['id' => $monthName->id]);
    }
}