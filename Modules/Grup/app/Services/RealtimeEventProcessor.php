<?php

namespace Modules\Grup\Services;

use Illuminate\Support\Facades\Validator;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\GroupReferral;
use Modules\Grup\Models\RealtimeEvent;

class RealtimeEventProcessor
{
    public function __construct(
        private readonly GroupHubClient $hub,
        private readonly MembershipSynchronizer $memberships,
    ) {}

    public function accept(array $payload): RealtimeEvent
    {
        $data = Validator::make($payload, [
            'event_id' => ['required', 'uuid'],
            'type' => ['required', 'in:membership.updated,patient.updated,referral.created,referral.updated'],
            'resource_id' => ['nullable', 'string', 'max:100'],
            'source_branch_id' => ['nullable', 'uuid'],
            'version' => ['required', 'integer', 'min:1'],
            'occurred_at' => ['required', 'date'],
        ])->validate();

        $branch = isset($data['source_branch_id'])
            ? Branch::where('hub_branch_id', $data['source_branch_id'])->first()
            : null;

        $event = RealtimeEvent::firstOrCreate(['event_id' => $data['event_id']], [
            'event_type' => $data['type'],
            'branch_id' => $branch?->id,
            'payload' => $data,
            'received_at' => now(),
        ]);

        if ($event->processed_at === null) {
            $this->process($event);
        }

        return $event;
    }

    public function process(RealtimeEvent $event): void
    {
        try {
            if ($event->event_type === 'membership.updated') {
                $this->memberships->sync();
            } elseif (str_starts_with($event->event_type, 'referral.')) {
                $this->syncReferral((string) ($event->payload['resource_id'] ?? ''));
            }

            // patient.updated sengaja hanya invalidasi/notifikasi. PHI tidak ada
            // di event dan baru diambil on-demand lewat REST ketika user membuka.
            $event->update(['processed_at' => now(), 'failure_reason' => null]);
        } catch (\Throwable $exception) {
            report($exception);
            $event->update(['failure_reason' => 'Sinkronisasi event gagal; akan dicoba ulang.']);
        }
    }

    private function syncReferral(string $id): void
    {
        abort_if($id === '', 422, 'Resource ID event rujukan wajib ada.');
        $data = $this->hub->referral($id);
        $local = Branch::where('is_local', true)->where('status', 'active')->firstOrFail();
        $source = Branch::where('hub_branch_id', $data['source_branch_id'])->where('group_id', $local->group_id)->firstOrFail();
        $destination = Branch::where('hub_branch_id', $data['destination_branch_id'])->where('group_id', $local->group_id)->firstOrFail();
        abort_unless($source->is($local) || $destination->is($local), 403, 'Rujukan bukan milik cabang lokal.');

        GroupReferral::updateOrCreate(['hub_referral_id' => $data['id']], [
            'group_id' => $local->group_id,
            'source_branch_id' => $source->id,
            'destination_branch_id' => $destination->id,
            'local_patient_id' => $source->is($local) ? $data['source_patient_id'] : null,
            'source_patient_id' => (string) $data['source_patient_id'],
            'patient_snapshot' => $data['patient_snapshot'],
            'reason' => $data['reason'],
            'clinical_summary' => $data['clinical_summary'] ?? null,
            'status' => $data['status'],
            'referred_at' => $data['referred_at'],
        ]);
    }
}
