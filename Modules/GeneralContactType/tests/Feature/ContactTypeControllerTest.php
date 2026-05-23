<?php

namespace Modules\GeneralContactType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralContactType\Models\ContactType;
use Tests\TestCase;

class ContactTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_contact_type(): void
    {
        $this->actingUser();
        ContactType::factory()->count(3)->create();

        $this->getJson('/api/v1/contact-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_contact_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/contact-types', ['name' => 'Contoh Jeniskontak', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskontak');

        $this->assertDatabaseHas('contact_types', ['name' => 'Contoh Jeniskontak']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ContactType::factory()->create(['name' => 'Contoh Jeniskontak']);

        $this->postJson('/api/v1/contact-types', ['name' => 'Contoh Jeniskontak'])->assertStatus(422);
    }

    public function test_it_deletes_contact_type(): void
    {
        $this->actingUser();
        $contactType = ContactType::factory()->create();

        $this->deleteJson("/api/v1/contact-types/{$contactType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('contact_types', ['id' => $contactType->id]);
    }
}