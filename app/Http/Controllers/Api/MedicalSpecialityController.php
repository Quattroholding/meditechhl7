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
            // Solo especialidades con al menos un practitioner con usuario activo
            ->whereHas('practitioners', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('active', true);
                });
            })
            ->orderBy('id')
            ->get()
            ->map(function ($speciality) {
                // Contar solo practitioners con usuarios activos
                $activePractitionersCount = $speciality->practitioners()
                    ->whereHas('user', function ($query) {
                        $query->where('active', true);
                    })
                    ->count();

                return [
                    'id' => $speciality->id,
                    'name' => $speciality->name,
                    'code' => $speciality->code ?? '',
                    'description' => $speciality->description ?? '',
                    'is_surgical' => $speciality->is_surgical ?? false,
                    'practitioners_count' => $activePractitionersCount,
                ];
            });

        return response()->json([
            'total' => $specialities->count(),
            'specialities' => $specialities,
        ]);
    }
}
