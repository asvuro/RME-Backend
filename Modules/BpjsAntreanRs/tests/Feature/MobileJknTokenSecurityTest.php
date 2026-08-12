<?php

namespace Modules\BpjsAntreanRs\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Modules\BpjsAntreanRs\Http\Middleware\VerifyBpjsMobileJknToken;
use Modules\BpjsAntreanRs\Models\MobileJknToken;
use Tests\TestCase;

/**
 * Regression port of the BpjsAntreanFktp hardening (findings S-1/S-2): the
 * antrean_rs Token endpoint must compare credentials timing-safely and persist
 * tokens only as sha256 digests that VerifyBpjsMobileJknToken can still
 * authenticate end-to-end (generate -> store -> verify).
 */
class MobileJknTokenSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'bpjs.families.antrean_rs.inbound_username' => 'rsuser',
            'bpjs.families.antrean_rs.inbound_password' => 'rspass',
        ]);
    }

    public function test_wrong_password_is_rejected_with_401(): void
    {
        $this->withHeaders(['x-username' => 'rsuser', 'x-password' => 'wrong'])
            ->getJson('/api/v1/antrean-rs/mobile-jkn/token')
            ->assertStatus(401)
            ->assertJsonPath('metadata.code', 401);

        $this->assertDatabaseCount('antrean_rs_mobile_jkn_tokens', 0);
    }

    public function test_correct_credentials_issue_token_stored_as_sha256_digest(): void
    {
        $response = $this->withHeaders(['x-username' => 'rsuser', 'x-password' => 'rspass'])
            ->getJson('/api/v1/antrean-rs/mobile-jkn/token');

        $response->assertOk()->assertJsonPath('metadata.code', 200);

        $plainText = $response->json('response.token');
        $this->assertNotEmpty($plainText);

        // Plaintext never lands in the database, only its sha256 digest.
        $this->assertDatabaseMissing('antrean_rs_mobile_jkn_tokens', ['token' => $plainText]);
        $record = MobileJknToken::where('username', 'rsuser')->first();
        $this->assertNotNull($record);
        $this->assertSame(hash('sha256', $plainText), $record->token);
        $this->assertFalse($record->isExpired());
    }

    public function test_issued_token_passes_jkn_middleware_and_survives_reuse(): void
    {
        Route::middleware([VerifyBpjsMobileJknToken::class])
            ->get('/_jkn-guarded-dummy', fn () => response()->json(['ok' => true]));

        $plainText = $this->withHeaders(['x-username' => 'rsuser', 'x-password' => 'rspass'])
            ->getJson('/api/v1/antrean-rs/mobile-jkn/token')
            ->json('response.token');

        $guardHeaders = ['x-username' => 'rsuser', 'x-token' => $plainText];

        // First call authenticates against the stored digest...
        $this->withHeaders($guardHeaders)
            ->getJson('/_jkn-guarded-dummy')
            ->assertOk()
            ->assertJson(['ok' => true]);

        // ...and the same plaintext token keeps working on later calls.
        $this->withHeaders($guardHeaders)
            ->getJson('/_jkn-guarded-dummy')
            ->assertOk();

        // A forged value that is not a known digest stays rejected.
        $this->withHeaders(['x-username' => 'rsuser', 'x-token' => str_repeat('a', 40)])
            ->getJson('/_jkn-guarded-dummy')
            ->assertStatus(401);
    }

    public function test_credential_and_token_comparisons_are_hardened(): void
    {
        // Static regression guard mirroring the Fktp module's hardened pattern:
        // hash_equals for credentials and digest storage/lookup for tokens.
        $moduleBase = dirname(__DIR__, 2);

        $controller = file_get_contents($moduleBase.'/app/Http/Controllers/MobileJknTokenController.php');
        $middleware = file_get_contents($moduleBase.'/app/Http/Middleware/VerifyBpjsMobileJknToken.php');

        $this->assertStringContainsString('hash_equals(', $controller);
        $this->assertStringNotContainsString('$password !== $config[', $controller);
        $this->assertStringContainsString("hash('sha256', \$plainTextToken)", $controller);
        $this->assertStringContainsString("hash('sha256', \$token)", $middleware);
        $this->assertStringNotContainsString("where('token', \$token)", $middleware);
    }
}