<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalSpeciality;
use Illuminate\Http\Request;

class MedicalSpecialityController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalSpeciality::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            // Solo especialidades con al menos un practitioner con usuario activo
            ->whereHas('practitioners', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('active', true);
                });
            })
            // Contar citas usando withCount para mejor performance
            ->withCount('appointments');

        // Ordenar por cantidad de citas si se solicita
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'desc');

        if ($orderBy === 'appointments_count') {
            $query->orderBy('appointments_count', $orderDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $specialities = $query->get()
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
                    'appointments_count' => $speciality->appointments_count,
                ];
            });

        return response()->json([
            'total' => $specialities->count(),
            'specialities' => $specialities,
        ]);
    }
}
