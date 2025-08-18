<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PractitionerResource;
use App\Models\Appointment;
use App\Models\Practitioner;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default 10 items per page
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $practitioners = Practitioner::with(['specialties', 'user.clients'])
            ->when($request->speciality_id, function ($query, $specialityId) {
                return $query->whereHas('specialties', function ($q) use ($specialityId) {
                    $q->where('medical_specialties.id', $specialityId);
                });
            })
            ->when($request->search, function ($query, $search) {
                $searchTerm = trim($search);
                if (empty($searchTerm)) {
                    return $query;
                }

                return $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                        ->orWhereRaw('LOWER(given_name) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                        ->orWhereRaw('LOWER(family_name) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                        ->orWhere('phone', 'like', "%$searchTerm%")
                        ->orWhere('identifier', 'like', "%$searchTerm%")
                        ->orWhereHas('specialties', function ($specialtyQuery) use ($searchTerm) {
                            $specialtyQuery->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($searchTerm).'%']);
                        })
                        ->orWhereHas('user.clients', function ($clientQuery) use ($searchTerm) {
                            $clientQuery->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($searchTerm).'%']);
                        });
                });
            })
            ->paginate($perPage);

        return response()->json([
            'data' => PractitionerResource::collection($practitioners->items()),
            'pagination' => [
                'current_page' => $practitioners->currentPage(),
                'per_page' => $practitioners->perPage(),
                'total' => $practitioners->total(),
                'last_page' => $practitioners->lastPage(),
                'from' => $practitioners->firstItem(),
                'to' => $practitioners->lastItem(),
                'has_more_pages' => $practitioners->hasMorePages(),
            ],
        ]);
    }

    public function availability(Request $request, $practitionerId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'duration' => 'integer|min:15|max:180',
        ]);

        $practitioner = Practitioner::findOrFail($practitionerId);
        $date = Carbon::parse($request->date);
        $duration = (int) ($request->duration ?? 30); // Default 30 minutes

        // Obtener citas existentes para ese día
        $existingAppointments = Appointment::where('practitioner_id', $practitionerId)
            ->whereDate('start', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('start')
            ->get(['start', 'end']);

        // Horarios de trabajo por defecto (esto debería venir de la configuración del médico)
        $workingHours = [
            'start' => '08:00',
            'end' => '17:00',
            'lunch_break' => [
                'start' => '12:00',
                'end' => '13:00',
            ],
        ];

        // Generar slots disponibles
        $availableSlots = $this->generateAvailableSlots(
            $date,
            $workingHours,
            $existingAppointments,
            $duration
        );

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'practitioner' => [
                'id' => $practitioner->id,
                'name' => $practitioner->name,
            ],
            'available_slots' => $availableSlots,
            'working_hours' => $workingHours,
            'total_slots' => count($availableSlots),
        ]);
    }

    private function generateAvailableSlots($date, $workingHours, $existingAppointments, $duration)
    {
        $slots = [];
        $slotDuration = $duration; // minutos

        // Crear los períodos de trabajo
        $workPeriods = [
            [
                'start' => $date->copy()->setTimeFromTimeString($workingHours['start']),
                'end' => $date->copy()->setTimeFromTimeString($workingHours['lunch_break']['start']),
            ],
            [
                'start' => $date->copy()->setTimeFromTimeString($workingHours['lunch_break']['end']),
                'end' => $date->copy()->setTimeFromTimeString($workingHours['end']),
            ],
        ];

        foreach ($workPeriods as $period) {
            $current = $period['start']->copy();

            while ($current->copy()->addMinutes($slotDuration)->lte($period['end'])) {
                $slotEnd = $current->copy()->addMinutes($slotDuration);

                // Verificar si no hay conflicto con citas existentes
                $hasConflict = $existingAppointments->contains(function ($appointment) use ($current, $slotEnd) {
                    $appointmentStart = Carbon::parse($appointment->start);
                    $appointmentEnd = Carbon::parse($appointment->end);

                    return $current->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
                });

                // Solo agregar si no hay conflicto y es en el futuro
                if (! $hasConflict && $current->gt(now())) {
                    $slots[] = [
                        'start_time' => $current->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'datetime' => $current->format('Y-m-d H:i:s'),
                        'available' => true,
                    ];
                }

                $current->addMinutes($slotDuration);
            }
        }

        return $slots;
    }

    public function consultingRooms($practitionerId)
    {
        $practitioner = Practitioner::with(['user.clients.branches.consultingRooms'])
            ->findOrFail($practitionerId);

        // Verificar si el practitioner tiene usuario y clientes asociados
        if (! $practitioner->user || ! $practitioner->user->clients) {
            return response()->json([
                'practitioner' => [
                    'id' => $practitioner->id,
                    'name' => $practitioner->name,
                ],
                'consulting_rooms' => [],
                'total' => 0,
            ]);
        }

        // Obtener todos los consultorios de todas las sucursales de todos los clientes
        $consultingRooms = collect();

        foreach ($practitioner->user->clients as $client) {
            foreach ($client->branches as $branch) {
                foreach ($branch->consultingRooms as $room) {
                    $consultingRooms->push([
                        'id' => $room->id,
                        'name' => $room->name,
                        'number' => $room->number,
                        'floor' => $room->floor,
                        'active' => $room->active,
                        'branch' => [
                            'id' => $branch->id,
                            'name' => $branch->name,
                            'address' => $branch->address,
                            'phone' => $branch->phone,
                        ],
                        'client' => [
                            'id' => $client->id,
                            'name' => $client->name,
                            'code' => $client->code ?? null,
                        ],
                        'full_name' => $room->name.' ('.$branch->name.')',
                    ]);
                }
            }
        }

        // Filtrar solo los consultorios activos
        $activeConsultingRooms = $consultingRooms->where('active', true)->values();

        return response()->json([
            'practitioner' => [
                'id' => $practitioner->id,
                'name' => $practitioner->name,
            ],
            'consulting_rooms' => $activeConsultingRooms,
            'total' => $activeConsultingRooms->count(),
        ]);
    }
}
