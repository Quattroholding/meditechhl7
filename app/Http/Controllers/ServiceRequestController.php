<?php

namespace App\Http\Controllers;

class ServiceRequestController extends Controller
{
    public function index()
    {
        return view('service_requests.index');
    }
}
