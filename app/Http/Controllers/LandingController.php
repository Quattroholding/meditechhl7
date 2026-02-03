<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function welcome()
    {
        return view('welcome');
    }
    public function index()
    {
        // Obtener todas las especialidades médicas
        $specialties = MedicalSpeciality::orderBy('name')->get();

        // Obtener practitioners activos que cumplan los requisitos:
        // 1. Practitioner activo
        // 2. Usuario activo
        // 3. Usuario con suscripción activa
        // 4. Existen consultorios activos en el sistema
        $practitioners = Practitioner::withoutGlobalScopes()
            ->with(['specialties', 'user', 'user.clients'])
            ->where('active', true)
            ->whereHas('user', function ($query) {
                $query->where('active', true)
                    // Usuario debe tener una suscripción activa
                    ->whereHas('clients', function ($clientQuery) {
                        $clientQuery->whereHas('subscription', function ($subQuery) {
                            $subQuery->whereIn('status', ['active', 'trial']);
                        });
                    });
            })
            ->get()
            ->filter(function ($practitioner) {
                // Verificar que exista al menos un consultorio activo en el sistema
                // (asociado al cliente del practitioner)
                $user = $practitioner->user;
                if (! $user) {
                    return false;
                }

                // Obtener el cliente del usuario
                $client = $user->getCurrentClient();
                if (! $client) {
                    return false;
                }

                // Verificar que el cliente tenga al menos un consultorio activo
                return ConsultingRoom::withoutGlobalScopes()
                    ->whereHas('branch', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })
                    ->where('active', true)
                    ->exists();
            });

        return view('sami', compact('specialties', 'practitioners'));
    }

    public function getPractitioners(Request $request)
    {
        $query = Practitioner::withoutGlobalScopes()
            ->with(['specialties', 'user', 'user.clients'])
            ->where('active', true)
            ->whereHas('user', function ($query) {
                $query->where('active', true)
                    // Usuario debe tener una suscripción activa
                    ->whereHas('clients', function ($clientQuery) {
                        $clientQuery->whereHas('subscription', function ($subQuery) {
                            $subQuery->whereIn('status', ['active', 'trial']);
                        });
                    });
            });

        // Filtrar por especialidad si se proporciona
        if ($request->has('specialty_id') && $request->specialty_id != '') {
            $query->whereHas('specialties', function ($q) use ($request) {
                $q->where('medical_specialties.id', $request->specialty_id);
            });
        }

        $practitioners = $query->get()
            ->filter(function ($practitioner) {
                // Verificar que exista al menos un consultorio activo
                $user = $practitioner->user;
                if (! $user) {
                    return false;
                }

                $client = $user->getCurrentClient();
                if (! $client) {
                    return false;
                }

                return ConsultingRoom::withoutGlobalScopes()
                    ->whereHas('branch', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })
                    ->where('active', true)
                    ->exists();
            });

        return response()->json($practitioners->values());
    }

    public function patientLanding()
    {
        // Obtener practitioners activos que cumplan los requisitos:
        // 1. Practitioner activo
        // 2. Usuario activo
        // 3. Usuario con suscripción activa
        // 4. Existen consultorios activos en el sistema
        $practitioners = Practitioner::withoutGlobalScopes()
            ->with(['specialties', 'user', 'user.clients'])
            ->where('active', true)
            ->whereHas('user', function ($query) {
                $query->where('active', true)
                    // Usuario debe tener una suscripción activa
                    ->whereHas('clients', function ($clientQuery) {
                        $clientQuery->whereHas('subscription', function ($subQuery) {
                            $subQuery->whereIn('status', ['active', 'trial']);
                        });
                    });
            })
            ->get()
            ->filter(function ($practitioner) {
                // Verificar que exista al menos un consultorio activo en el sistema
                // (asociado al cliente del practitioner)
                $user = $practitioner->user;
                if (! $user) {
                    return false;
                }

                // Obtener el cliente del usuario
                $client = $user->getCurrentClient();
                if (! $client) {
                    return false;
                }

                // Verificar que el cliente tenga al menos un consultorio activo
                return ConsultingRoom::withoutGlobalScopes()
                    ->whereHas('branch', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })
                    ->where('active', true)
                    ->exists();
            });

        // Obtener solo las especialidades que tienen al menos un practitioner disponible
        $specialtyIds = $practitioners->pluck('specialties')->flatten()->pluck('id')->unique();
        $specialties = MedicalSpeciality::whereIn('id', $specialtyIds)
            ->orderBy('name')
            ->get();

        $whatsappNumber = '5078316172'; // SAMI WhatsApp number
        $whatsappMessage = urlencode('¡Hola! Quiero agendar una cita médica');

        return view('patients-landing', compact('specialties', 'practitioners', 'whatsappNumber', 'whatsappMessage'));
    }
}
