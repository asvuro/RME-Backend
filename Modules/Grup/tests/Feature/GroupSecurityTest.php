<?php

namespace Modules\Grup\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Auth\Models\User;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\Group;
use Modules\Grup\Services\GroupHubClient;
use Modules\Grup\Services\MembershipSynchronizer;
use Tests\TestCase;

class GroupSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'grup.hub_url' => 'https://hub.example.test',
            'grup.hub_token' => 'test-token',
            'grup.hub_hmac_secret' => 'test-hmac-secret',
            'grup.instance_id' => 'instance-local',
        ]);
    }

    public function test_membership_is_replaced_only_from_authoritative_hub_snapshot(): void
    {
        $groupId = (string) Str::uuid();
        $localId = (string) Str::uuid();
        Http::fake(['https://hub.example.test/api/v1/group/context' => Http::response(['data' => [
            'group' => ['id' => $groupId, 'legal_name' => 'PT Arjuna', 'legal_identifier' => 'NIB-01', 'status' => 'active'],
            'branches' => [
                ['id' => $localId, 'instance_id' => 'instance-local', 'code' => 'ARJ-01', 'name' => 'Klinik Arjuna 1', 'status' => 'active'],
                ['id' => (string) Str::uuid(), 'instance_id' => 'instance-sibling', 'code' => 'ARJ-02', 'name' => 'Klinik Arjuna 2', 'status' => 'active'],
            ],
        ]])]);

        $group = app(MembershipSynchronizer::class)->sync();

        $this->assertSame('PT Arjuna', $group->legal_name);
        $this->assertSame(2, Branch::count());
        $this->assertDatabaseHas('grup_branches', ['hub_branch_id' => $localId, 'is_local' => true]);
        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://hub.example.test/')
            && $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_hub_client_never_uses_caller_controlled_url(): void
    {
        Http::fake(['https://hub.example.test/*' => Http::response(['data' => []])]);

        app(GroupHubClient::class)->searchPatients([
            'q' => 'Budi',
            'url' => 'http://127.0.0.1:9000/private',
        ]);

        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://hub.example.test/')
            && ! str_contains($request->url(), '127.0.0.1'));
    }

    public function test_hub_client_rejects_url_with_embedded_credentials(): void
    {
        config(['grup.hub_url' => 'https://hub.example.test@127.0.0.1']);
        Http::fake();

        $this->expectException(\RuntimeException::class);
        try {
            app(GroupHubClient::class)->context();
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_signed_hub_ingress_rejects_replay_and_wrong_group(): void
    {
        [$group, $local] = $this->localMembership();
        $payload = json_encode([
            'event_id' => (string) Str::uuid(),
            'type' => 'patient.updated',
            'resource_id' => '99',
            'source_branch_id' => $local->hub_branch_id,
            'version' => 1,
            'occurred_at' => now()->toIso8601String(),
        ], JSON_THROW_ON_ERROR);
        $requestId = (string) Str::uuid();
        $timestamp = (string) time();
        $headers = $this->signedHeaders($payload, $requestId, $timestamp, $group->hub_group_id, $local->instance_id);

        $this->call('POST', '/api/v1/grup/relay/notifications', [], [], [], $headers, $payload)->assertOk();
        $this->call('POST', '/api/v1/grup/relay/notifications', [], [], [], $headers, $payload)->assertStatus(409);

        $wrongId = (string) Str::uuid();
        $wrongHeaders = $this->signedHeaders($payload, $wrongId, $timestamp, (string) Str::uuid(), $local->instance_id);
        $this->call('POST', '/api/v1/grup/relay/notifications', [], [], [], $wrongHeaders, $payload)->assertStatus(403);
    }

    public function test_user_cannot_route_patient_request_to_branch_of_another_group(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        [, $local] = $this->localMembership();
        $otherGroup = Group::create(['hub_group_id' => (string) Str::uuid(), 'legal_name' => 'PT Lain', 'status' => 'active']);
        $foreign = Branch::create([
            'group_id' => $otherGroup->id,
            'hub_branch_id' => (string) Str::uuid(),
            'instance_id' => 'foreign-instance',
            'code' => 'OTHER',
            'name' => 'Klinik Lain',
            'status' => 'active',
        ]);
        $user = User::factory()->create();
        $user->assignRole('admin');

        Http::fake();
        $this->actingAs($user, 'sanctum')->getJson("/api/v1/grup/patients/{$foreign->hub_branch_id}/1")
            ->assertNotFound();
        Http::assertNothingSent();
        $this->assertTrue($local->is_local);
    }

    private function localMembership(): array
    {
        $group = Group::create(['hub_group_id' => (string) Str::uuid(), 'legal_name' => 'PT Arjuna', 'status' => 'active']);
        $local = Branch::create([
            'group_id' => $group->id,
            'hub_branch_id' => (string) Str::uuid(),
            'instance_id' => 'instance-local',
            'code' => 'LOCAL',
            'name' => 'Klinik Lokal',
            'status' => 'active',
            'is_local' => true,
        ]);

        return [$group, $local];
    }

    private function signedHeaders(string $body, string $requestId, string $timestamp, string $groupId, string $target): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RME_TIMESTAMP' => $timestamp,
            'HTTP_X_RME_REQUEST_ID' => $requestId,
            'HTTP_X_RME_GROUP_ID' => $groupId,
            'HTTP_X_RME_TARGET_INSTANCE_ID' => $target,
            'HTTP_X_RME_SIGNATURE' => hash_hmac('sha256', $timestamp."\n".$requestId."\n".$body, 'test-hmac-secret'),
        ];
    }
}
