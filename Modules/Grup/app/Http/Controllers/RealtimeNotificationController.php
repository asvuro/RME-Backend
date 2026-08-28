<?php

namespace Modules\Grup\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Grup\Models\RealtimeEvent;
use Modules\Grup\Services\RealtimeEventProcessor;

class RealtimeNotificationController extends Controller
{
    public function store(Request $request, RealtimeEventProcessor $processor): JsonResponse
    {
        $event = $processor->accept($request->all());

        return response()->json(['success' => true, 'event_id' => $event->event_id]);
    }

    public function index(Request $request): JsonResponse
    {
        $after = $request->date('after');
        $events = RealtimeEvent::query()->when($after, fn ($q) => $q->where('received_at', '>', $after))
            ->latest('received_at')->limit(100)->get(['event_id', 'event_type', 'received_at', 'processed_at']);

        return response()->json(['data' => $events]);
    }
}
