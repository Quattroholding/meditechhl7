<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalSpeciality;
use Illuminate\Http\Request;

class MedicalSpecialityController extends Controller
{
    public function index(Request $request)
    {
        $specialities = MedicalSpeciality::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('id')
            ->get()
            ->map(function ($speciality) {
                return [
                    'id' => $speciality->id,
                    'name' => $speciality->name,
                    'code' => $speciality->code ?? '',
                    'description' => $speciality->description ?? '',
                    'is_surgical' => $speciality->is_surgical ?? false,
                    'practitioners_count' => $speciality->practitioners()->count(),
                ];
            });

        return response()->json([
            'total' => $specialities->count(),
            'specialities' => $specialities,
        ]);
    }
}
