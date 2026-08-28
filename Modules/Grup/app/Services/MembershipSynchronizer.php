<?php

namespace Modules\Grup\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\Group;

class MembershipSynchronizer
{
    public function __construct(private readonly GroupHubClient $hub) {}

    public function sync(): Group
    {
        $payload = $this->hub->context();
        $validated = Validator::make($payload, [
            'group.id' => ['required', 'uuid'],
            'group.legal_name' => ['required', 'string', 'max:255'],
            'group.legal_identifier' => ['nullable', 'string', 'max:255'],
            'group.status' => ['required', 'in:active,suspended,revoked'],
            'branches' => ['required', 'array', 'min:1', 'max:500'],
            'branches.*.id' => ['required', 'uuid'],
            'branches.*.instance_id' => ['required', 'string', 'max:255', 'distinct'],
            'branches.*.code' => ['required', 'string', 'max:64', 'distinct'],
            'branches.*.name' => ['required', 'string', 'max:255'],
            'branches.*.status' => ['required', 'in:active,suspended,revoked'],
            'branches.*.capabilities' => ['sometimes', 'array'],
            'branches.*.last_seen_at' => ['nullable', 'date'],
        ])->validate();

        $instanceId = (string) config('grup.instance_id');
        if (! collect($validated['branches'])->contains('instance_id', $instanceId)) {
            throw ValidationException::withMessages(['branches' => 'Hub tidak mengembalikan instance lokal sebagai anggota grup.']);
        }

        return DB::transaction(function () use ($validated, $instanceId) {
            $group = Group::updateOrCreate(
                ['hub_group_id' => $validated['group']['id']],
                [
                    'legal_name' => $validated['group']['legal_name'],
                    'legal_identifier' => $validated['group']['legal_identifier'] ?? null,
                    'status' => $validated['group']['status'],
                    'synced_at' => now(),
                ],
            );

            $activeIds = [];
            foreach ($validated['branches'] as $branch) {
                $model = Branch::updateOrCreate(
                    ['hub_branch_id' => $branch['id']],
                    [
                        'group_id' => $group->id,
                        'instance_id' => $branch['instance_id'],
                        'code' => $branch['code'],
                        'name' => $branch['name'],
                        'status' => $branch['status'],
                        'is_local' => hash_equals($instanceId, $branch['instance_id']),
                        'capabilities' => $branch['capabilities'] ?? [],
                        'last_seen_at' => $branch['last_seen_at'] ?? null,
                    ],
                );
                $activeIds[] = $model->id;
            }

            // Tidak menghapus riwayat/FK. Cabang yang hilang dari snapshot hub
            // dinonaktifkan sehingga tidak lagi menjadi target pertukaran.
            Branch::where('group_id', $group->id)->whereNotIn('id', $activeIds)
                ->update(['status' => 'revoked', 'is_local' => false]);

            return $group->load('branches');
        });
    }
}
