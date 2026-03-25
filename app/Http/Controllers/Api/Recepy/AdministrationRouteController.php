<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\AdministrationRoute;
use Illuminate\Http\JsonResponse;

class AdministrationRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $routes = AdministrationRoute::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }
}
