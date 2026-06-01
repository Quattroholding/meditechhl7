<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\AdministrationRoute;

class AdministrationRouteController extends Controller
{
    public function index()
    {
        $routes = AdministrationRoute::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }
}
