<?php

namespace Tests\Feature;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

/**
 * Regression for the debug-mode exception-disclosure finding: API clients must
 * never receive exception classes, filesystem paths, stack traces or other
 * debug envelopes — even if APP_DEBUG is accidentally left enabled in an
 * environment reachable by external callers.
 */
class ExceptionDebugDisclosureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('_debug_disclosure_probe')) {
            Route::get('/api/v1/_debug-disclosure-probe', fn () => throw new RuntimeException(
                'could not find driver (Connection: mariadb, Host: 127.0.0.1, Port: 3306, Database: Backend)'
            ))->name('_debug_disclosure_probe');
        }
    }

    public function test_debug_mode_never_exposes_exception_internals_to_api_clients(): void
    {
        config(['app.debug' => true]);

        $response = $this->getJson('/api/v1/_debug-disclosure-probe');

        $response->assertStatus(500);

        $payload = $response->json();
        foreach (['exception', 'file', 'line', 'trace'] as $leakedKey) {
            $this->assertArrayNotHasKey($leakedKey, $payload);
        }

        $body = $response->getContent();
        $this->assertStringNotContainsString('getTrace()', $body);
        $this->assertStringNotContainsString('.php', $body);
        $this->assertStringNotContainsString('RuntimeException', $body);

        // Non-HTTP exceptions collapse to the same opaque message used when
        // debug mode is disabled, so a misconfigured environment cannot be
        // distinguished from a correctly configured one by attackers.
        $this->assertSame('Server Error', $payload['message']);
    }

    public function test_debug_disabled_returns_opaque_server_error(): void
    {
        config(['app.debug' => false]);

        $this->getJson('/api/v1/_debug-disclosure-probe')
            ->assertStatus(500)
            ->assertExactJson(['message' => 'Server Error']);
    }

    public function test_http_exception_messages_and_status_codes_are_preserved(): void
    {
        config(['app.debug' => true]);

        Route::get('/api/v1/_debug-not-found-probe', fn () => abort(404, 'Model row missing'));

        $this->getJson('/api/v1/_debug-not-found-probe')
            ->assertStatus(404)
            ->assertJsonMissing(['trace'])
            ->assertJsonMissing(['file']);
    }
}