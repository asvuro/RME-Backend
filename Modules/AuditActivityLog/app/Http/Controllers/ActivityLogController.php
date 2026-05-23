<?php

namespace Modules\AuditActivityLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\AuditActivityLog\Http\Resources\ActivityLogResource;
use Modules\AuditActivityLog\Models\ActivityLog;

/** Akses jejak aktivitas: admin saja (jejak audit bukan konsumsi umum). */
class ActivityLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ActivityLog::query()->with('user');

        $query->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')));
        $query->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')));
        $query->when($request->filled('object'), fn ($q) => $q->where('object', $request->string('object')));
        $query->when($request->filled('ref'), fn ($q) => $q->where('ref', $request->string('ref')));
        $query->when($request->filled('date_from'), fn ($q) => $q->where('created_at', '>=', $request->date('date_from')));
        $query->when($request->filled('date_to'), fn ($q) => $q->where('created_at', '<=', $request->date('date_to')));

        return ActivityLogResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }
}
