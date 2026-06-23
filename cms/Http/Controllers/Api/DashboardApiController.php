<?php

namespace Cms\Http\Controllers\Api;

use Cms\Http\Controllers\Controller;
use Cms\Support\AdminStats;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => AdminStats::dashboard(),
        ]);
    }
}
