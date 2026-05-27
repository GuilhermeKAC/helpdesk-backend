<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->dashboardService->getStats($request->user());

        return response()->json(['data' => $stats]);
    }

    public function charts(Request $request): JsonResponse
    {
        $charts = $this->dashboardService->getCharts($request->user());

        return response()->json(['data' => $charts]);
    }
}
