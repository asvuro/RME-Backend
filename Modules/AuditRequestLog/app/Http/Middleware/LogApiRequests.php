<?php

namespace Modules\AuditRequestLog\Http\Middleware;

use App\Modules\Contracts\HospitalConfig;
use Closure;
use Illuminate\Http\Request;
use Modules\AuditRequestLog\Models\RequestLog;
use Symfony\Component\HttpFoundation\Response;

/**
 * Jejak request API masuk — port semangat bridge_log simgos2. Hanya path
 * api/* (health /up dilewat), payload input dibatasi ke field referensi
 * yang aman dan dibatasi panjang; response hanya statusnya yang dicatat.
 */
class LogApiRequests
{
    /** Batas jumlah karakter per nilai payload yang disimpan. */
    protected const MAX_VALUE_LENGTH = 5000;

    /** Field body yang boleh masuk audit log. Hindari data klinis/pasien mentah. */
    protected const ALLOWED_PAYLOAD_KEYS = [
        'id',
        'uuid',
        'ulid',
        'code',
        'kode',
        'ref',
        'ref_id',
        'ref_code',
        'reference',
        'reference_id',
        'reference_code',
        'external_id',
        'external_code',
        'request_id',
        'transaction_id',
    ];

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
                'url' => mb_substr($request->url(), 0, self::MAX_VALUE_LENGTH),
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
            return null;
        }

        $input = $this->filterAllowedPayload($request->input());

        return $input === [] ? null : $input;
    }

    /**
     * @param  array<string|int, mixed>  $input
     * @return array<string|int, mixed>
     */
    protected function filterAllowedPayload(array $input): array
    {
        $filtered = [];

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $nested = $this->filterAllowedPayload($value);

                if ($nested !== []) {
                    $filtered[$key] = $nested;
                }

                continue;
            }

            if (! $this->isAllowedPayloadKey($key)) {
                continue;
            }

            $filtered[$key] = $this->normalizePayloadValue($value);
        }

        return $filtered;
    }

    protected function isAllowedPayloadKey(string|int $key): bool
    {
        if (is_int($key)) {
            return false;
        }

        $key = strtolower($key);

        return in_array($key, self::ALLOWED_PAYLOAD_KEYS, true)
            || str_ends_with($key, '_id');
    }

    protected function normalizePayloadValue(mixed $value): mixed
    {
        if (! is_string($value) || strlen($value) <= self::MAX_VALUE_LENGTH) {
            return $value;
        }

        return substr($value, 0, self::MAX_VALUE_LENGTH).'…[truncated]';
    }
}
