<?php

namespace Modules\AuditRequestLog\Http\Middleware;

use App\Modules\Contracts\HospitalConfig;
use Closure;
use Illuminate\Http\Request;
use Modules\AuditRequestLog\Models\RequestLog;
use Symfony\Component\HttpFoundation\Response;

/**
 * Jejak request API masuk — port semangat bridge_log simgos2. Hanya path
 * api/* (health /up dilewat), payload input direduksi field sensitif dan
 * dibatasi panjang; response hanya statusnya yang dicatat.
 */
class LogApiRequests
{
    /** Batas jumlah karakter per nilai payload yang disimpan. */
    protected const MAX_VALUE_LENGTH = 5000;

    public function __construct(protected HospitalConfig $config) {}

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        $response = $next($request);

        try {
            if (! $this->enabled() || ! $request->is('api/*')) {
                return $response;
            }

            RequestLog::create([
                'method' => $request->getMethod(),
                'url' => mb_substr($request->fullUrl(), 0, self::MAX_VALUE_LENGTH),
                'status' => $response->getStatusCode(),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'user_id' => $request->user()?->getAuthIdentifier(),
                'ip' => $request->ip(),
                'payload' => $this->preparePayload($request),
            ]);
        } catch (\Throwable) {
            // Jejak audit gagal tak boleh merusak respons API.
        }

        return $response;
    }

    protected function enabled(): bool
    {
        return (bool) $this->config->get('audit.request_log_enabled', true);
    }

    /** @return array<string, mixed>|null */
    protected function preparePayload(Request $request): ?array
    {
        if ($request->getMethod() === 'GET') {
            return null; // query string sudah terwakili di url.
        }

        $input = $request->except(['password', 'password_confirmation', 'token',
            'remember_token', 'authorization']);

        array_walk_recursive($input, function (&$value): void {
            if (is_string($value) && strlen($value) > self::MAX_VALUE_LENGTH) {
                $value = substr($value, 0, self::MAX_VALUE_LENGTH).'…[truncated]';
            }
        });

        return $input === [] ? null : $input;
    }
}
