<?php

namespace Modules\Grup\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroupHubClient
{
    public function context(): array
    {
        return $this->request()->get($this->url('/api/v1/group/context'))->throw()->json('data');
    }

    public function searchPatients(array $query): array
    {
        return $this->request()->get(
            $this->url('/api/v1/group/relay/patients'),
            Arr::only($query, ['branch_id', 'q', 'page', 'per_page']),
        )->throw()->json();
    }

    public function patient(string $branchId, string $patientId): array
    {
        return $this->request()->get($this->url("/api/v1/group/relay/branches/{$branchId}/patients/{$patientId}"))->throw()->json();
    }

    public function referrals(array $query = []): array
    {
        return $this->request()->get($this->url('/api/v1/group/relay/referrals'), $query)->throw()->json();
    }

    public function referral(string $referralId): array
    {
        return $this->request()->get($this->url("/api/v1/group/relay/referrals/{$referralId}"))->throw()->json('data');
    }

    public function createReferral(array $payload, string $idempotencyKey): array
    {
        return $this->request()->withHeader('Idempotency-Key', $idempotencyKey)
            ->post($this->url('/api/v1/group/relay/referrals'), $payload)->throw()->json('data');
    }

    public function updateReferral(string $referralId, array $payload, string $idempotencyKey): array
    {
        return $this->request()->withHeader('Idempotency-Key', $idempotencyKey)
            ->patch($this->url("/api/v1/group/relay/referrals/{$referralId}"), $payload)->throw()->json('data');
    }

    public function realtimeAuth(string $socketId, string $channel): array
    {
        return $this->request()->post($this->url('/api/v1/group/realtime/auth'), [
            'socket_id' => $socketId,
            'channel_name' => $channel,
        ])->throw()->json();
    }

    protected function request(): PendingRequest
    {
        $token = (string) config('grup.hub_token');
        $instanceId = (string) config('grup.instance_id');

        if ($token === '' || $instanceId === '') {
            throw new RuntimeException('Kredensial Grup Hub belum dikonfigurasi.');
        }

        return Http::acceptJson()
            ->asJson()
            ->withToken($token)
            ->withHeaders(['X-RME-Instance-ID' => $instanceId])
            ->connectTimeout((int) config('grup.connect_timeout', 5))
            ->timeout((int) config('grup.timeout', 15))
            ->retry(2, 200, throw: false);
    }

    protected function url(string $path): string
    {
        $base = rtrim((string) config('grup.hub_url'), '/');
        $parts = parse_url($base);
        $local = app()->environment(['local', 'testing']);

        if (! is_array($parts)
            || empty($parts['host'])
            || (! $local && ($parts['scheme'] ?? null) !== 'https')
            || ! in_array($parts['scheme'] ?? null, ['http', 'https'], true)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])) {
            throw new RuntimeException('Konfigurasi GRUP_HUB_URL tidak aman.');
        }

        return $base.$path;
    }
}
