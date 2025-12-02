<?php

namespace App\Http\Controllers;

use App\Models\Patient;

class DermatologyController extends Controller
{
    /**
     * Display patient selection for dermatology
     */
    public function index()
    {
        return view('dermatology.index');
    }

    /**
     * Show dermatology component for specific patient
     */
    public function show(Patient $patient)
    {
        return view('dermatology.show', compact('patient'));
    }
}
