<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        return view('service_requests.index');
    }
}
