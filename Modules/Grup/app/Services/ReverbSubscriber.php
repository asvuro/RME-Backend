<?php

namespace Modules\Grup\Services;

use RuntimeException;

/**
 * Subscriber kecil untuk protokol Pusher/Reverb. Dijalankan sebagai proses
 * supervisor (`php artisan grup:listen`), bukan di lifecycle request web.
 */
class ReverbSubscriber
{
    public function __construct(
        private readonly GroupHubClient $hub,
        private readonly RealtimeEventProcessor $events,
    ) {}

    public function listen(): void
    {
        $instanceId = (string) config('grup.instance_id');
        $channel = (string) config('grup.reverb.channel_prefix').$instanceId;
        $stream = $this->connect();

        try {
            $connected = $this->nextJson($stream);
            if (($connected['event'] ?? null) !== 'pusher:connection_established') {
                throw new RuntimeException('Reverb tidak mengirim connection_established.');
            }

            $connectionData = json_decode((string) ($connected['data'] ?? ''), true, flags: JSON_THROW_ON_ERROR);
            $auth = $this->hub->realtimeAuth((string) $connectionData['socket_id'], $channel);
            $this->writeJson($stream, [
                'event' => 'pusher:subscribe',
                'data' => ['channel' => $channel, 'auth' => $auth['auth']],
            ]);

            while (is_resource($stream) && ! feof($stream)) {
                $message = $this->nextJson($stream);
                if (($message['event'] ?? null) === 'pusher:ping') {
                    $this->writeJson($stream, ['event' => 'pusher:pong', 'data' => new \stdClass]);

                    continue;
                }
                if (($message['event'] ?? null) !== 'grup.notification') {
                    continue;
                }

                $payload = is_string($message['data'] ?? null)
                    ? json_decode($message['data'], true, flags: JSON_THROW_ON_ERROR)
                    : ($message['data'] ?? []);
                $this->events->accept($payload);
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /** @return resource */
    private function connect()
    {
        $scheme = (string) config('grup.reverb.scheme', 'wss');
        $host = (string) config('grup.reverb.host');
        $port = (int) config('grup.reverb.port', 443);
        $key = (string) config('grup.reverb.app_key');
        if ($host === '' || $key === '' || ! in_array($scheme, ['ws', 'wss'], true)) {
            throw new RuntimeException('Konfigurasi Reverb Grup belum lengkap.');
        }
        if (! app()->environment(['local', 'testing']) && $scheme !== 'wss') {
            throw new RuntimeException('Reverb production wajib memakai wss.');
        }

        $transport = $scheme === 'wss' ? 'ssl' : 'tcp';
        $stream = @stream_socket_client("{$transport}://{$host}:{$port}", $errorCode, $errorMessage, 10);
        if (! is_resource($stream)) {
            throw new RuntimeException("Tidak dapat terhubung ke Reverb ({$errorCode}).");
        }
        stream_set_timeout($stream, 60);

        $path = '/app/'.rawurlencode($key).'?protocol=7&client=rme-backend&version=1.0&flash=false';
        $nonce = base64_encode(random_bytes(16));
        $request = "GET {$path} HTTP/1.1\r\nHost: {$host}:{$port}\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: {$nonce}\r\nSec-WebSocket-Version: 13\r\nOrigin: https://{$host}\r\n\r\n";
        fwrite($stream, $request);

        $headers = '';
        while (strlen($headers) < 16384) {
            $line = fgets($stream, 4096);
            if ($line === false) {
                throw new RuntimeException('Handshake Reverb terputus.');
            }
            $headers .= $line;
            if ($line === "\r\n") {
                break;
            }
        }
        $expected = base64_encode(sha1($nonce.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        if (! str_starts_with($headers, 'HTTP/1.1 101') || ! str_contains(strtolower($headers), strtolower("Sec-WebSocket-Accept: {$expected}"))) {
            throw new RuntimeException('Handshake Reverb ditolak.');
        }

        return $stream;
    }

    /** @param resource $stream */
    private function nextJson($stream): array
    {
        while (true) {
            [$opcode, $payload] = $this->readFrame($stream);
            if ($opcode === 0x8) {
                throw new RuntimeException('Koneksi Reverb ditutup.');
            }
            if ($opcode === 0x9) {
                $this->writeFrame($stream, $payload, 0xA);

                continue;
            }
            if ($opcode === 0x1) {
                return json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
            }
        }
    }

    /** @param resource $stream */
    private function readFrame($stream): array
    {
        $head = $this->readExact($stream, 2);
        $first = ord($head[0]);
        $second = ord($head[1]);
        $length = $second & 0x7F;
        if ($length === 126) {
            $length = unpack('n', $this->readExact($stream, 2))[1];
        } elseif ($length === 127) {
            $parts = unpack('Nhigh/Nlow', $this->readExact($stream, 8));
            if ($parts['high'] !== 0) {
                throw new RuntimeException('Frame Reverb terlalu besar.');
            }
            $length = $parts['low'];
        }
        if ($length > 1048576) {
            throw new RuntimeException('Frame Reverb melebihi batas 1 MiB.');
        }
        $mask = ($second & 0x80) !== 0 ? $this->readExact($stream, 4) : null;
        $payload = $this->readExact($stream, $length);
        if ($mask !== null) {
            for ($i = 0; $i < $length; $i++) {
                $payload[$i] = $payload[$i] ^ $mask[$i % 4];
            }
        }

        return [$first & 0x0F, $payload];
    }

    /** @param resource $stream */
    private function writeJson($stream, array $data): void
    {
        $this->writeFrame($stream, json_encode($data, JSON_THROW_ON_ERROR));
    }

    /** @param resource $stream */
    private function writeFrame($stream, string $payload, int $opcode = 0x1): void
    {
        $length = strlen($payload);
        $head = chr(0x80 | $opcode);
        if ($length < 126) {
            $head .= chr(0x80 | $length);
        } elseif ($length <= 65535) {
            $head .= chr(0x80 | 126).pack('n', $length);
        } else {
            $head .= chr(0x80 | 127).pack('NN', 0, $length);
        }
        $mask = random_bytes(4);
        $masked = '';
        for ($i = 0; $i < $length; $i++) {
            $masked .= $payload[$i] ^ $mask[$i % 4];
        }
        fwrite($stream, $head.$mask.$masked);
    }

    /** @param resource $stream */
    private function readExact($stream, int $length): string
    {
        $result = '';
        while (strlen($result) < $length) {
            $chunk = fread($stream, $length - strlen($result));
            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('Koneksi Reverb terputus saat membaca frame.');
            }
            $result .= $chunk;
        }

        return $result;
    }
}
