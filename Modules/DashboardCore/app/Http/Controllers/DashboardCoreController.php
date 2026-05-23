<?php

namespace Modules\DashboardCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\DashboardCore\Services\DashboardCoreService;

class DashboardCoreController extends Controller
{
    public function __construct(private readonly DashboardCoreService $dashboard)
    {
    }

    public function core(Request $request): JsonResponse
    {
        $request->validate(['date' => ['nullable', 'date']]);

        return response()->json(['data' => $this->dashboard->core($request->input('date'))]);
    }
}
