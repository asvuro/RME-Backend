<?php

namespace Modules\AuditActivityLog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AuditActivityLog\Models\ActivityLog;

/** @mixin ActivityLog */
class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', fn () => $this->user?->name),
            'action' => $this->action,
            'object' => $this->object,
            'ref' => $this->ref,
            'before' => $this->before,
            'after' => $this->after,
            'ip' => $this->ip,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
