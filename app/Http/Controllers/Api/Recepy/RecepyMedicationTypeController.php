<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\MedicationType;

class RecepyMedicationTypeController extends Controller
{
    /**
     * Get all medication types
     */
    public function index()
    {
        $medicationTypes = MedicationType::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $medicationTypes,
        ]);
    }
}
