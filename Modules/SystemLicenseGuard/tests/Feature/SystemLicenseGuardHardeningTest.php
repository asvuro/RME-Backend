<?php

namespace Modules\SystemLicenseGuard\tests\Feature;

use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Auth\Models\User;
use Modules\SystemLicenseGuard\Models\SystemLicenseWebhookEvent;
use Modules\SystemLicenseGuard\Services\HardwareFingerprintService;
use Tests\TestCase;

/**
 * Regression tests for pentest findings: SSRF lewat central_hub_url,
 * disclosure HWID tanpa auth, replay webhook, dan supersede non-atomik.
 */
class SystemLicenseGuardHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected string $privateKey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCml7jXqi271Bd7
8HTIEsNZon3VDIp9xFp0H2D7LS9YTg9wgiTshO5zjwXPdYPXAY5gnK3v5762CoWt
8xQfrPjlXn/tKLfmrdz0SjX9a3TJ2sY3sU3m0CZ8pZshmxbzb+vJIvv6wLzFHRMZ
9Qtv2csVrJh5LR2B0s0e3YwmGTCxPkKPd0Xubebl9eK7IzungSUeQrQ8nTgkH2vz
B7IY+NQsnAKJ/f7P5X3tfu5QB2kL16yvQ3t3jO0zyKE5p2oReCqMB+iwBdiK4o4o
KPL+f8RuniVQZ6KYBzpE5k9H9lMuOforPGp2LgejdHhwydZFfVHSgxK9K3LlQRaR
x1oSSA1vAgMBAAECggEAFTDqjeBzZdeiTx00tbiRSeceC4pHWZ0vBmNxcjH6ukhf
tWeQq9dy/5cbXZrstN5ZTpfPlltLyptHCuwHTMozdPXS3weVcQ3/s8F24bAYEI84
NKBksO4cXzGvrJQuVLQRcan2FJ0MxiQfKGef11AEgdLNKGjhPoDin8D90WDV7U2y
HVDmSgI3FQb2h1csWFW/fcq0UWhRtjGXsQ05XruGJBVwGu2IXlY8X8VRUDujjNqc
bMXRwevOwlMSsNDt9xGd+5qJ3TLkbo7MYe035Xj4waw1L3uSXknNyuYq5LouZsD9
TIZABs2+1KjTKBTQzWBRXYUwy5kQdkfX1TSlK7xJjQKBgQDRoXN3BxwefbviA4eB
FZxQXddalz8QgM+QPUUOCrCR6hjCM3apSn3jcdEetdzrOao1BRQyiFTrK/dErWgw
OXl0RJ1vPiRut1rIGopnj+iaETHToH4smjvzFnjrGs3NXrf3JFROVWpdO4JB6BMY
wWVxSgQbpuyfU05SLVHJE8KoGwKBgQDLcTNPjxgj+NC7oSeB5b1342+pTMkP7kL6
yEueJVEXYz7P33Puwnr5U1pfsxJoT17abST3SfmqG8iMOdZk+T/jjTcdnqFKViA2
LYG+5K71GM5zsNc1ma361Y8CCSp0ECrn8RotoU0Q4fX+7JQDQ5oiaFPHkn7h1sGd
b/m9JB/tPQKBgBsPiGQ5Grwk5vgKFJfpPkBxnLcpBw8OUb0A68UgRCLR4VhQZGT+
JLoTmzcnqtkCnKIzgaP0TWH+TvEioWx4PuFvahNOJk3UhXeHVY6A2TnqNlBnS2Je
lpaOtBVFZIO6Um0o12k1RYG7iNkRKEXt0TaKo3UeWzVF/7pD92bJkjs5AoGBAMNf
BbCcsckx1Hqry74CPA8bOotycxA6dtZRbdUE7zgvlg2ZAMPEvsYbVwnadY340xWe
hUZ6IypKnjCUBqFXgBRt9AUc5rX1ud9tmlERWjeESBdwN2yBFkRxvHFvDfcB48J0
lvhFFDZnWY8j8Qylaisq13Ir7G/DhpJEC6ipPYPdAoGBAMDaQkYgfPbPtOM2Ht/D
aDAzjerIOPegHWugIXzDWyXuW7cIiPCeUXmNesTCqBJSsU1++KTBNHQsejhh0LwT
fTxRvFiK4AvuRf77Kz4VEcHGzEBiURYY+7iNtpH/jCOHQ1g38E/uk8fX8bBG+yW4
9HYwMDF1/gfLC9AUzcWL/WRT
-----END PRIVATE KEY-----
EOD;

    protected string $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAppe416otu9QXe/B0yBLD
WaJ91QyKfcRadB9g+y0vWE4PcIIk7ITuc48Fz3WD1wGOYJyt7+e+tgqFrfMUH6z4
5V5/7Si35q3c9Eo1/Wt0ydrGN7FN5tAmfKWbIZsW82/rySL7+sC8xR0TGfULb9nL
FayYeS0dgdLNHt2MJhkwsT5Cj3dF7m3m5fXiuyM7p4ElHkK0PJ04JB9r8weyGPjU
LJwCif3+z+V97X7uUAdpC9esr0N7d4ztM8ihOadqEXgqjAfosAXYiuKOKCjy/n/E
bp4lUGeimAc6ROZPR/ZTLjn6Kzxqdi4Ho3R4cMnWRX1R0oMSvSty5UEWkcdaEkgN
bwIDAQAB
-----END PUBLIC KEY-----
EOD;

    protected function setUp(): void
    {
        parent::setUp();

        config(['license.public_key' => $this->publicKey]);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    protected function hwid(): string
    {
        return app(HardwareFingerprintService::class)->getFingerprint();
    }

    protected function signPayload(array $payload): string
    {
        $payloadJson = json_encode($payload);
        openssl_sign($payloadJson, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($payloadJson).'.'.base64_encode($signature);
    }

    protected function licenseToken(string $code = 'RSHS-001', ?string $validUntil = null): string
    {
        if ($code === 'FRESH') {
            // instance_id sengaja deterministik supaya uji konflik bisa
            // menabrak UNIQUE(instance_id) dengan token lain.
            return $this->signPayload([
                'instance_id' => 'INST-FRESH',
                'client_name' => 'RS Harapan Sehat',
                'client_code' => 'FRESH',
                'license_key' => 'LIC-FRESH-'.strtoupper(bin2hex(random_bytes(6))),
                'hardware_id' => $this->hwid(),
                'tier' => 'enterprise',
                'issued_at' => Carbon::now()->toIso8601String(),
                'valid_until' => $validUntil ?? Carbon::now()->addYear()->toIso8601String(),
                'max_users' => 100,
                'allowed_modules' => ['SatuSehatIgd'],
            ]);
        }

        return $this->signPayload([
            'instance_id' => 'INST-'.$code.'-'.uniqid(),
            'client_name' => 'RS Harapan Sehat',
            'client_code' => $code,
            'license_key' => 'LIC-'.$code.'-'.strtoupper(bin2hex(random_bytes(6))),
            'hardware_id' => $this->hwid(),
            'tier' => 'enterprise',
            'issued_at' => Carbon::now()->toIso8601String(),
            'valid_until' => $validUntil ?? Carbon::now()->addYear()->toIso8601String(),
            'max_users' => 100,
            'allowed_modules' => ['SatuSehatIgd'],
        ]);
    }

    protected function signedEnvelope(array $body, string $secret = 'rahasia-hub'): array
    {
        $raw = json_encode($body);

        return [
            'raw' => $raw,
            'headers' => [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $raw, $secret),
            ],
        ];
    }

    public function test_status_and_fingerprint_require_authenticated_admin(): void
    {
        $this->getJson('/api/v1/system/license/status')->assertStatus(401);
        $this->getJson('/api/v1/system/license/fingerprint')->assertStatus(401);

        $petugas = User::factory()->create();
        $petugas->assignRole('petugas');
        $petugasHeaders = $this->actingAs($petugas, 'sanctum');
        $petugasHeaders->getJson('/api/v1/system/license/status')->assertStatus(403);
        $petugasHeaders->getJson('/api/v1/system/license/fingerprint')->assertStatus(403);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $adminHeaders = $this->actingAs($admin, 'sanctum');
        $adminHeaders->getJson('/api/v1/system/license/status')->assertOk()
            ->assertJsonPath('success', false)
            ->assertJsonPath('verification_code', 'NO_LICENSE');
        $adminHeaders->getJson('/api/v1/system/license/fingerprint')->assertOk()
            ->assertJsonPath('success', true);
        $this->assertStringStartsWith(
            'HWID-',
            (string) $adminHeaders->getJson('/api/v1/system/license/fingerprint')->json('fingerprint'),
        );
    }

    public function test_activate_rejects_caller_supplied_central_hub_url_without_outbound_request(): void
    {
        Http::fake();

        $response = $this->postJson('/api/v1/system/license/activate', [
            'license_key' => 'ANY-KEY',
            'central_hub_url' => 'http://127.0.0.1:9911',
        ]);

        // Permintaan keluar hanya boleh ke hub hasil konfigurasi server -
        // nilai central_hub_url dari pemanggil diabaikan total.
        foreach (Http::recorded() as [$sent]) {
            $this->assertStringStartsWith(
                (string) config('license.central_hub_url'),
                $sent->url(),
            );
            $this->assertStringNotContainsString('127.0.0.1:9911', $sent->url());
        }

        $this->assertSame(422, $response->status());
    }

    public function test_activation_failures_never_echo_internal_exception_text(): void
    {
        config(['license.central_hub_url' => 'http://127.0.0.1:9911']);
        Http::fake(['*127.0.0.1:9911*' => Http::response(
            ['message' => 'Internal LDAP bind failed at ldap://10.42.0.9'],
            500,
        )]);

        $response = $this->postJson('/api/v1/system/license/activate', ['license_key' => 'ANY']);

        $this->assertSame(422, $response->status());
        $this->assertStringNotContainsString(
            (string) $response->getContent(),
            'ldap://10.42.0.9',
        );
    }

    public function test_webhook_requires_event_id_and_fresh_timestamp(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $stale = $this->signedEnvelope([
            'event' => 'license.suspended',
            'event_id' => uniqid('', true),
            'timestamp' => time() - 3600,
        ]);
        $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $stale['headers'], $stale['raw'])
            ->assertStatus(403);

        $missingEventId = $this->signedEnvelope([
            'event' => 'license.suspended',
            'timestamp' => time(),
        ]);
        $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $missingEventId['headers'], $missingEventId['raw'])
            ->assertStatus(403);
    }

    public function test_webhook_replay_is_rejected_via_persisted_event_dedupe(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $envelopeBody = [
            'event' => 'license.updated',
            'token' => $this->licenseToken(),
            'event_id' => 'evt-01HZX-replay-check',
            'timestamp' => time(),
        ];
        $first = $this->signedEnvelope($envelopeBody);
        $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $first['headers'], $first['raw'])
            ->assertOk()
            ->assertJsonPath('success', true);

        // Persis envelope yang sama (HMAC identik) diputar ulang.
        $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $first['headers'], $first['raw'])
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->assertSame(1, SystemLicenseWebhookEvent::count());
    }

    public function test_webhook_activation_conflict_is_atomic_and_leaks_nothing(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $freshToken = $this->licenseToken('FRESH');
        $this->postJson('/api/v1/system/license/activate', ['license_token' => $freshToken])->assertOk();

        // Token bertanda tangan valid tetapi menabrak UNIQUE(instance_id):
        // persis kondisi yang dulu membuat supersede non-atomik meninggalkan
        // instans tanpa lisensi aktif sambil membocorkan SQL mentah.
        $conflictPayload = [
            'instance_id' => 'INST-FRESH',
            'client_name' => 'RS Harapan Sehat',
            'client_code' => 'EVIL',
            'license_key' => 'LIC-EVIL-KEY-'.strtoupper(bin2hex(random_bytes(4))),
            'hardware_id' => $this->hwid(),
            'tier' => 'enterprise',
            'issued_at' => Carbon::now()->toIso8601String(),
            'valid_until' => Carbon::now()->addYear()->toIso8601String(),
            'max_users' => 100,
            'allowed_modules' => ['*'],
        ];
        $envelopeBody = [
            'event' => 'license.updated',
            'token' => $this->signPayload($conflictPayload),
            'event_id' => uniqid('evt-', true),
            'timestamp' => time(),
        ];
        $envelope = $this->signedEnvelope($envelopeBody);
        $replay = $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $envelope['headers'], $envelope['raw']);

        $replay->assertStatus(422)->assertJsonPath('success', false);
        $this->assertStringNotContainsString((string) $replay->getContent(), 'UNIQUE constraint failed');
        $this->assertStringNotContainsString((string) $replay->getContent(), 'HWID-');
        $this->assertStringNotContainsString((string) $replay->getContent(), 'SQLSTATE');

        // Transaksi rollback: lisensi lama tetap aktif, baris konflik tidak tercipta.
        $this->assertSame(1, \Modules\SystemLicenseGuard\Models\SystemLicense::where('client_code', 'FRESH')->where('status', 'active')->count());
        $this->assertDatabaseMissing('system_licenses', ['client_code' => 'EVIL']);
    }

    public function test_replayed_license_webhook_is_idempotent_upsert_without_duplicates(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $token = $this->licenseToken('RPL');
        $body = [
            'event' => 'license.updated',
            'token' => $token,
        ];

        foreach ([1, 2] as $attempt) {
            $envelopeBody = array_merge($body, ['event_id' => 'evt-rpl-'.$attempt, 'timestamp' => time()]);
            $envelope = $this->signedEnvelope($envelopeBody);
            $this->call('POST', '/api/v1/system/license/webhook', [], [], [], $envelope['headers'], $envelope['raw'])
                ->assertOk()
                ->assertJsonPath('success', true);
        }

        $this->assertSame(1, \Modules\SystemLicenseGuard\Models\SystemLicense::count());
    }
}