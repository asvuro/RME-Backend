<?php

namespace Modules\SystemLicenseGuard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\SystemLicenseGuard\Models\SystemLicenseWebhookEvent;
use Modules\SystemLicenseGuard\Http\Requests\ActivateLicenseRequest;
use Modules\SystemLicenseGuard\Http\Requests\SyncLicenseRequest;
use Modules\SystemLicenseGuard\Http\Resources\LicenseStatusResource;
use Modules\SystemLicenseGuard\Services\CentralHubClientService;
use Modules\SystemLicenseGuard\Services\HardwareFingerprintService;
use Modules\SystemLicenseGuard\Services\LicenseVerifierService;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseVerifierService $verifier,
        protected HardwareFingerprintService $fingerprintService,
        protected CentralHubClientService $hubClient
    ) {}

    public function status(): JsonResponse
    {
        $verification = $this->verifier->verify();
        $license = $verification['license'] ?? $this->verifier->getActiveLicense();

        return response()->json([
            'success' => $verification['valid'],
            'verification_code' => $verification['code'],
            'message' => $verification['message'],
            'fingerprint' => $this->fingerprintService->getFingerprint(),
            'data' => new LicenseStatusResource($license),
        ]);
    }

    public function fingerprint(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'fingerprint' => $this->fingerprintService->getFingerprint(),
        ]);
    }

    public function activate(ActivateLicenseRequest $request): JsonResponse
    {
        if ($request->filled('license_token')) {
            try {
                $license = $this->verifier->activateToken($request->input('license_token'));

                return response()->json([
                    'success' => true,
                    'message' => 'License activated successfully via cryptographic token.',
                    'data' => new LicenseStatusResource($license),
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => 'Activation failed via cryptographic token.',
                ], 422);
            }
        }

        $result = $this->hubClient->activateOnline(
            $request->input('license_key'),
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => new LicenseStatusResource($result['license']),
        ]);
    }

    public function sync(SyncLicenseRequest $request): JsonResponse
    {
        $result = $this->hubClient->sendHeartbeat();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'verification' => $this->verifier->verify(),
        ], $result['success'] ? 200 : 502);
    }

    public function webhook(Request $request): JsonResponse
    {
        // Fail-closed: tanpa secret terkonfigurasi, webhook ditolak total - bukan
        // dilewati. Kalau tidak, deployment yang lupa mengisi SAAS_WEBHOOK_SECRET
        // mempublikasikan endpoint yang bisa men-suspend instance / menyuntik
        // token lisensi tanpa kredensial apa pun.
        $secret = config('license.webhook_secret');
        if (empty($secret)) {
            return response()->json(['success' => false, 'message' => 'Webhook is not configured on this instance.'], 403);
        }

        $signature = $request->header('X-Hub-Signature-256');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($expected, (string) $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook signature.'], 403);
        }

        // Anti-replay: event_id unik + timestamp di dalam body yang ikut di-HMAC.
        // Tanpa itu, satu webhook yang terekam bisa diputar ulang selamanya.
        $eventId = trim((string) $request->input('event_id'));
        $timestamp = $request->input('timestamp');

        if ($eventId === '' || !is_numeric($timestamp)) {
            return response()->json(['success' => false, 'message' => 'Webhook envelope requires event_id and timestamp.'], 403);
        }

        $tolerance = max(1, (int) config('license.webhook_timestamp_tolerance', 300));
        if (abs(time() - (int) $timestamp) > $tolerance) {
            return response()->json(['success' => false, 'message' => 'Webhook timestamp outside allowed tolerance.'], 403);
        }

        try {
            SystemLicenseWebhookEvent::query()->create([
                'event_id' => $eventId,
                'event_type' => $request->input('event'),
                'processed_at' => now(),
            ]);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Webhook already processed.'], 409);
        }

        $event = $request->input('event');
        $token = $request->input('token');

        if ($event === 'license.updated' && $token) {
            try {
                $license = $this->verifier->activateToken($token);
                return response()->json([
                    'success' => true,
                    'message' => 'License updated from central hub webhook.',
                    'data' => new LicenseStatusResource($license),
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json(['success' => false, 'message' => 'License activation failed.'], 422);
            }
        }

        if ($event === 'license.suspended') {
            $license = $this->verifier->getActiveLicense();
            if ($license) {
                $license->update(['status' => 'suspended']);
            }
            return response()->json(['success' => true, 'message' => 'Instance suspended.']);
        }

        return response()->json(['success' => true, 'message' => 'Event received.']);
    }
}
