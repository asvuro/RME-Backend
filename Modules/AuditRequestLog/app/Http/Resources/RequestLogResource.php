<?php

namespace Modules\AuditRequestLog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AuditRequestLog\Models\RequestLog;

/** @mixin RequestLog */
class RequestLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'url' => $this->url,
            'status' => $this->status,
            'duration_ms' => $this->duration_ms,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', fn () => $this->user?->name),
            'ip' => $this->ip,
            'payload' => $this->payload,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
