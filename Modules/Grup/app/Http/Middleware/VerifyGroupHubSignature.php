<?php

namespace Modules\Grup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Grup\Models\Branch;
use Symfony\Component\HttpFoundation\Response;

class VerifyGroupHubSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('grup.hub_hmac_secret');
        $timestamp = (string) $request->header('X-RME-Timestamp', '');
        $requestId = (string) $request->header('X-RME-Request-ID', '');
        $signature = (string) $request->header('X-RME-Signature', '');

        abort_if($secret === '' || ! ctype_digit($timestamp), 403, 'Signature hub tidak valid.');
        abort_if(abs(time() - (int) $timestamp) > (int) config('grup.timestamp_tolerance', 300), 403, 'Request hub kedaluwarsa.');
        abort_if(! preg_match('/^[0-9a-fA-F-]{36}$/', $requestId), 403, 'Request ID hub tidak valid.');

        $expected = hash_hmac('sha256', $timestamp."\n".$requestId."\n".$request->getContent(), $secret);
        abort_if(! hash_equals($expected, $signature), 403, 'Signature hub tidak valid.');

        $inserted = DB::table('grup_hub_nonces')->insertOrIgnore([
            'request_id' => $requestId,
            'received_at' => now(),
        ]);
        abort_if($inserted === 0, 409, 'Request hub sudah pernah diproses.');

        $local = Branch::query()->where('is_local', true)->where('status', 'active')->with('group')->first();
        abort_if($local === null || $local->group?->status !== 'active', 403, 'Keanggotaan grup lokal tidak aktif.');
        abort_if(! hash_equals((string) $local->group->hub_group_id, (string) $request->header('X-RME-Group-ID')), 403, 'Grup sumber tidak cocok.');
        abort_if(! hash_equals((string) $local->instance_id, (string) $request->header('X-RME-Target-Instance-ID')), 403, 'Target instance tidak cocok.');

        return $next($request);
    }
}
