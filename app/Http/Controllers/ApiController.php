<?php

namespace App\Http\Controllers;

use App\Models\CptCode;
use App\Models\Icd10Code;
use App\Models\MedicalSpeciality;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\Scopes\PractitionerScope;
use App\Models\ServiceCatalog;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function patients(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = 'id,name';
        }

        $data = Patient::selectRaw($select)
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(identifier LIKE '%".$request->q."%' or name LIKE '%".$request->q."%')");
            })
            ->take(10)
            ->get();

        return response()->json($data);

    }

    public function users(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = "id,concat(first_name,' ',last_name)  as name";
        }

        $data = User::selectRaw($select)
            ->when($request->has('role_id'), function ($q) use ($request) {
                $q->whereHas('roles', function ($q2) use ($request) {
                    $q2->where('roles.id', $request->role_id);
                });
            })
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(first_name LIKE '%".$request->q."%' or last_name LIKE '%".$request->q."%')");
            })
            ->take(10)
            ->get();

        return response()->json($data);

    }

    public function diagnostics(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = "id,code,concat(code,'|',description_es)  as name,description_es,description";
        }

        $query = Icd10Code::selectRaw($select)
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(code LIKE '%".$request->q."%' or description LIKE '%".$request->q."%' or description_es LIKE '%".$request->q."%')");
            });

        if ($request->has('ramdom')) {
            $data = $query->inRandomOrder()->take(1)->first();
        } else {
            $data = $query->take(10)->get();
        }

        return response()->json($data);

    }

    public function cpts(Request $request, $type)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = "id,concat(code,'|',description_es)  as name,description_es,description,code";
        }

        $query = CptCode::selectRaw($select)
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(code LIKE '%".$request->q."%' or description LIKE '%".$request->q."%' or description_es LIKE '%".$request->q."%')");
            })
            ->whereType($type);

        if ($request->has('ramdom')) {
            $data = $query->inRandomOrder()->take(1)->first();

            return response()->json($data);
        }

        // Soporte para paginación
        $perPage = $request->get('perPage', 50);
        $searchQuery = $request->get('q', '');

        // Detectar si es búsqueda por código (empieza con dígito)
        $isCodeSearch = preg_match('/^[0-9]/', $searchQuery);

        // Contar total de resultados
        $totalResults = $query->count();

        // Búsqueda inteligente con ordenamiento por relevancia
        if ($isCodeSearch && strlen($searchQuery) > 0) {
            // Búsqueda por código: priorizar coincidencias en código
            $query->orderByRaw('
                CASE
                    WHEN code = ? THEN 1
                    WHEN code LIKE ? THEN 2
                    ELSE 3
                END,
                code ASC
            ', [$searchQuery, "{$searchQuery}%"]);
        } elseif (strlen($searchQuery) > 0) {
            // Búsqueda por descripción: priorizar coincidencias en descripción
            $query->orderByRaw('
                CASE
                    WHEN code = ? THEN 1
                    WHEN description_es = ? THEN 2
                    WHEN description_es LIKE ? THEN 3
                    WHEN code LIKE ? THEN 4
                    ELSE 5
                END,
                code ASC
            ', [$searchQuery, $searchQuery, "{$searchQuery}%", "{$searchQuery}%"]);
        } else {
            // Sin búsqueda, ordenar por código
            $query->orderBy('code');
        }

        $data = $query->take($perPage)->get();

        return response()->json([
            'data' => $data,
            'total' => $totalResults,
            'perPage' => $perPage,
            'hasMore' => $totalResults > $perPage,
            'isCodeSearch' => $isCodeSearch,
        ]);

    }

    public function medicalSpeciality(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = 'id,name';
        }

        $query = MedicalSpeciality::selectRaw($select)
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(id LIKE '%".$request->q."%' or name LIKE '%".$request->q."%')");
            });

        if ($request->has('ramdom')) {
            $data = $query->inRandomOrder()->take(1)->first();

            return response()->json($data);
        }

        // Soporte para paginación
        $perPage = $request->get('perPage', 50);
        $searchQuery = $request->get('q', '');

        // Contar total de resultados
        $totalResults = $query->count();

        // Búsqueda inteligente con ordenamiento por relevancia
        if (strlen($searchQuery) > 0) {
            $query->orderByRaw('
                CASE
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END,
                name ASC
            ', [$searchQuery, "{$searchQuery}%"]);
        } else {
            // Sin búsqueda, ordenar por nombre
            $query->orderBy('name');
        }

        $data = $query->take($perPage)->get();

        return response()->json([
            'data' => $data,
            'total' => $totalResults,
            'perPage' => $perPage,
            'hasMore' => $totalResults > $perPage,
        ]);

    }

    public function medicines(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = "id,concat(home_name,' de ',mgs,' ',mgs_type,' en ',type) as name";
        }

        $query = Medicine::selectRaw($select)
            ->where(function ($q) {
                // Always show FDA medicines (public)
                $q->where('source', 'FDA');

                // If user is authenticated, also show their custom medicines
                if (auth()->check()) {
                    $q->orWhere(function ($q2) {
                        $q2->where('source', 'CUSTOM')
                            ->where('user_id', auth()->id());
                    });
                }
            })
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(ndc_code LIKE '%".$request->q."%' or home_name LIKE '%".$request->q."%' or generic_name LIKE '%".$request->q."%')");
            });

        if ($request->has('ramdom')) {
            $data = $query->inRandomOrder()->take(1)->first();
        } else {
            $data = $query->take(10)->get();
        }

        return response()->json($data);

    }

    public function practitioners(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = 'id,name';
        }

        $query = Practitioner::selectRaw($select)
            ->when($request->has('referral'), function ($q) {
                $q->withoutGlobalScope(PractitionerScope::class);
            })
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(id LIKE '%".$request->q."%' or name LIKE '%".$request->q."%')");
            })->when($request->has('speciality_id'), function ($q) use ($request) {
                $q->whereHas('qualifications', function ($q2) use ($request) {
                    $q2->where('practitioner_qualifications.medical_speciality_id', $request->speciality_id);
                });
            });

        if ($request->has('ramdom')) {
            $data = $query->inRandomOrder()->take(1)->first();
        } else {
            $data = $query->take(10)->get();
        }

        return response()->json($data);
    }

    public function servicesCatalog(Request $request)
    {
        $select = '*';

        if ($request->has('dropdown')) {
            $select = 'id,name';
        }

        $data = ServiceCatalog::selectRaw($select)
            ->when($request->has('q'), function ($q) use ($request) {
                $q->whereRaw("(cpt_code LIKE '%".$request->q."%' or name LIKE '%".$request->q."%')");
            })
            ->take(10)
            ->get();

        return response()->json($data);

    }

    public function statesByCountry($country_id)
    {
        $states = State::where('country_id', $country_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($states);
    }
}
