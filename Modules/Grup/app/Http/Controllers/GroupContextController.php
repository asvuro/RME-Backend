<?php

namespace Modules\Grup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Grup\Models\Branch;
use Modules\Grup\Models\Group;
use Modules\Grup\Services\MembershipSynchronizer;

class GroupContextController extends Controller
{
    public function show(): JsonResponse
    {
        $group = Group::query()->latest('synced_at')->with(['branches' => fn ($q) => $q->orderBy('code')])->first();

        return response()->json(['data' => $group ? $this->serialize($group) : null]);
    }

    public function sync(MembershipSynchronizer $sync): JsonResponse
    {
        return response()->json(['data' => $this->serialize($sync->sync())]);
    }

    private function serialize(Group $group): array
    {
        return [
            'id' => $group->hub_group_id,
            'legal_name' => $group->legal_name,
            'legal_identifier' => $group->legal_identifier,
            'status' => $group->status,
            'synced_at' => $group->synced_at?->toIso8601String(),
            'branches' => $group->branches->map(fn (Branch $branch) => [
                'id' => $branch->hub_branch_id,
                'instance_id' => $branch->instance_id,
                'code' => $branch->code,
                'name' => $branch->name,
                'status' => $branch->status,
                'is_local' => $branch->is_local,
                'last_seen_at' => $branch->last_seen_at?->toIso8601String(),
            ])->values(),
        ];
    }
}
