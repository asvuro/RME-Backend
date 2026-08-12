<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

spl_autoload_register(static function (string $class): void {
    if (! str_starts_with($class, 'Modules\\')) {
        return;
    }

    $rel = substr($class, 8);
    [$module, $rest] = array_pad(explode('\\', $rel, 2), 2, '');
    $mapping = [
        'Database\Factories\\' => 'database/factories/',
        'Database\Seeders\\' => 'database/seeders/',
        'Tests\\' => 'tests/',
    ];

    $subdir = 'app/';

    foreach ($mapping as $prefix => $dir) {
        if (str_starts_with($rest, $prefix)) {
            $subdir = $dir;
            $rest = substr($rest, strlen($prefix));
            break;
        }
    }

    $path = __DIR__.'/../Modules/'.$module.'/'.$subdir.str_replace('\\', '/', $rest).'.php';

    if (is_file($path)) {
        require_once $path;
    }
});

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Jejak request API (#12) — port semangat logs.bridge_log simgos2.
        // RoutePermissionGate (RBAC dinamis): gerbang izin per-aksi terpusat,
        // menggantikan middleware role:... yang tersebar di tiap file rute
        // secara bertahap (lihat rbac-dynamic-permission-plan). Aman aktif
        // global sebelum semua modul dimigrasi -- lihat docblock kelasnya.
        $middleware->api(append: [
            \Modules\AuditRequestLog\Http\Middleware\LogApiRequests::class,
            \Modules\Authorization\Http\Middleware\RoutePermissionGate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Defense-in-depth: even if APP_DEBUG is accidentally left on in a
        // publicly reachable environment, JSON error payloads must never carry
        // exception internals (class, file, line, trace — which embed SQL and
        // database connection details). Legitimate messages (401 Unauthenticated,
        // validation errors, HTTP exception messages) are left untouched.
        $exceptions->respond(function (Response $response, Throwable $exception): Response {
            if (! $response instanceof JsonResponse) {
                return $response;
            }

            $payload = $response->getData(true);

            if (! is_array($payload)) {
                return $response;
            }

            $hadDebugEnvelope = false;

            foreach (['exception', 'file', 'line', 'trace'] as $internalKey) {
                $hadDebugEnvelope = $hadDebugEnvelope || array_key_exists($internalKey, $payload);
                unset($payload[$internalKey]);
            }

            // A debug envelope also implies the raw exception message (for
            // example a QueryException carrying host/port/database name); collapse
            // it to the same opaque message used when debugging is disabled.
            if ($hadDebugEnvelope && ! $exception instanceof HttpExceptionInterface) {
                $payload['message'] = 'Server Error';
            }

            $response->setData($payload);

            return $response;
        });
    })->create();
