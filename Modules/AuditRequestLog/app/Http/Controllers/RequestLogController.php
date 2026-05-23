<?php

namespace Modules\AuditRequestLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\AuditRequestLog\Http\Resources\RequestLogResource;
use Modules\AuditRequestLog\Models\RequestLog;

/** Akses jejak request API: admin saja. */
class RequestLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = RequestLog::query()->with('user');

        $query->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')));
        $query->when($request->filled('method'), fn ($q) => $q->where('method', strtoupper($request->string('method'))));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->integer('status')));
        $query->when($request->filled('date_from'), fn ($q) => $q->where('created_at', '>=', $request->date('date_from')));
        $query->when($request->filled('date_to'), fn ($q) => $q->where('created_at', '<=', $request->date('date_to')));

        return RequestLogResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }
}
