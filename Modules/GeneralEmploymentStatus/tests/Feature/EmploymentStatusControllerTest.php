<?php

namespace Modules\GeneralEmploymentStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmploymentStatus\Models\EmploymentStatus;
use Tests\TestCase;

class EmploymentStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_employment_statuse(): void
    {
        $this->actingUser();
        EmploymentStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/employment-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_employment_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/employment-statuses', ['name' => 'Contoh Statuskepegawaian', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statuskepegawaian');

        $this->assertDatabaseHas('employment_statuses', ['name' => 'Contoh Statuskepegawaian']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        EmploymentStatus::factory()->create(['name' => 'Contoh Statuskepegawaian']);

        $this->postJson('/api/v1/employment-statuses', ['name' => 'Contoh Statuskepegawaian'])->assertStatus(422);
    }

    public function test_it_deletes_employment_status(): void
    {
        $this->actingUser();
        $employmentStatus = EmploymentStatus::factory()->create();

        $this->deleteJson("/api/v1/employment-statuses/{$employmentStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('employment_statuses', ['id' => $employmentStatus->id]);
    }
}