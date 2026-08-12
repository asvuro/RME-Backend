<?php

namespace Modules\SystemLicenseGuard\tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SystemLicenseGuard\Models\SystemLicense;
use Modules\SystemLicenseGuard\Services\HardwareFingerprintService;
use Tests\TestCase;

/**
 * Regression test temuan security-review K-2 (2026-08-25): webhook lisensi
 * dulunya fail-open - bila SAAS_WEBHOOK_SECRET kosong, verifikasi HMAC
 * dilewati sehingga siapa pun bisa men-suspend instance atau menyuntik token
 * lisensi lewat endpoint publik. Sekarang wajib fail-closed.
 */
class LicenseWebhookSecurityTest extends TestCase
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

    protected string $hwid;

    protected function setUp(): void
    {
        parent::setUp();

        config(['license.public_key' => $this->publicKey]);
        $this->hwid = app(HardwareFingerprintService::class)->getFingerprint();
    }

    protected function generateSignedToken(): string
    {
        $payload = [
            'instance_id' => 'INST-' . uniqid(),
            'client_name' => 'RS Harapan Sehat',
            'client_code' => 'RSHS-001',
            'license_key' => 'LIC-' . strtoupper(bin2hex(random_bytes(8))),
            'hardware_id' => $this->hwid,
            'tier' => 'enterprise',
            'issued_at' => Carbon::now()->toIso8601String(),
            'valid_until' => Carbon::now()->addYear()->toIso8601String(),
            'max_users' => 100,
            'allowed_modules' => [],
        ];

        $payloadJson = json_encode($payload);
        openssl_sign($payloadJson, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($payloadJson) . '.' . base64_encode($signature);
    }

    protected function signBody(string $body, string $secret): array
    {
        // Envelope wajib membawa event_id + timestamp yang ikut di-HMAC.
        $envelope = is_array($decoded = json_decode($body, true)) ? $decoded : [];
        $envelope += [
            'event_id' => uniqid('evt-', true),
            'timestamp' => time(),
        ];
        $signedBody = json_encode($envelope);

        return [
            'X-Hub-Signature-256' => 'sha256=' . hash_hmac('sha256', $signedBody, $secret),
            '__raw_body__' => $signedBody,
        ];
    }

    public function test_it_rejects_webhook_entirely_when_secret_is_not_configured(): void
    {
        config(['license.webhook_secret' => '']);

        // Event destruktif paling menarik bagi penyerang - harus tetap ditolak.
        $response = $this->postJson('/api/v1/system/license/webhook', [
            'event' => 'license.suspended',
        ]);

        $response->assertStatus(403)->assertJsonPath('success', false);
    }

    public function test_it_rejects_webhook_with_invalid_signature(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $response = $this->postJson(
            '/api/v1/system/license/webhook',
            ['event' => 'license.suspended'],
            ['X-Hub-Signature-256' => 'sha256=' . str_repeat('0', 64)]
        );

        $response->assertStatus(403)->assertJsonPath('success', false);
    }

    public function test_it_suspends_license_on_properly_signed_webhook(): void
    {
        config(['license.webhook_secret' => 'rahasia-hub']);

        $this->postJson('/api/v1/system/license/activate', [
            'license_token' => $this->generateSignedToken(),
        ])->assertOk();

        $this->assertDatabaseHas('system_licenses', ['client_code' => 'RSHS-001', 'status' => 'active']);

        $headers = $this->signBody(json_encode(['event' => 'license.suspended']), 'rahasia-hub');
        $rawBody = $headers['__raw_body__'];
        unset($headers['__raw_body__']);
        $headers['HTTP_X_HUB_SIGNATURE_256'] = $headers['X-Hub-Signature-256'];

        $this->call(
            'POST',
            '/api/v1/system/license/webhook',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'] + $headers,
            $rawBody,
        )->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('system_licenses', ['client_code' => 'RSHS-001', 'status' => 'suspended']);
    }
}
