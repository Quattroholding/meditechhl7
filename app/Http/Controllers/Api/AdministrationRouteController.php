<?php

namespace App\Http\Controllers\Api;

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
        $routes = AdministrationRoute::all();
        return response()->json($routes);
    }
}
