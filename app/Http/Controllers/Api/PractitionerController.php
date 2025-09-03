<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PractitionerResource;
use App\Models\Appointment;
use App\Models\Practitioner;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default 10 items per page
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $practitioners = Practitioner::with(['specialties', 'user.clients'])
            ->when($request->id, function ($query) use ($request) {
                return $query->whereId($request->id);
            })
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

        // Calcular fechas de la próxima semana (lunes a domingo)
        $nextWeekStart = now();
        $nextWeekEnd = now()->endOfWeek();

        // Obtener horarios ocupados para cada practitioner
        $practitionersWithSchedule = $practitioners->getCollection()->map(function ($practitioner) use ($nextWeekStart, $nextWeekEnd) {
            // Obtener citas ocupadas de la próxima semana con estados específicos

            $bookedAppointments = Appointment::where('practitioner_id', $practitioner->id)
                ->whereBetween('start', [$nextWeekStart, $nextWeekEnd])
                ->whereIn('status', ['booked', 'arrived', 'fulfilled'])
                ->orderBy('start')
                ->get(['start', 'end', 'status'])
                ->map(function ($appointment) {
                    return [
                        'start' => $appointment->start->format('Y-m-d H:i'),
                        'end' => $appointment->end->format('Y-m-d H:i'),
                        'date' => $appointment->start->format('Y-m-d'),
                        'start_time' => $appointment->start->format('H:i'),
                        'end_time' => $appointment->end->format('H:i'),
                        'status' => $appointment->status,
                        'day_of_week' => $appointment->start->format('l'), // Monday, Tuesday, etc.
                        'day_of_week_es' => $this->getDayNameInSpanish($appointment->start->format('l')),
                    ];
                });

            // Agregar los horarios ocupados al practitioner
            $practitioner->next_week_schedule = [
                'week_start' => $nextWeekStart->format('Y-m-d'),
                'week_end' => $nextWeekEnd->format('Y-m-d'),
                'booked_appointments' => $bookedAppointments->toArray(),
                'total_appointments' => $bookedAppointments->count(),
                'busy_days' => $bookedAppointments->pluck('date')->unique()->values()->toArray(),
            ];

            return $practitioner;
        });

        $practitioners->setCollection(collect($practitionersWithSchedule));

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
            'next_week_info' => [
                'start_date' => $nextWeekStart->format('Y-m-d'),
                'end_date' => $nextWeekEnd->format('Y-m-d'),
                'week_dates' => $this->getWeekDates($nextWeekStart),
            ],
        ]);
    }

    public function availability(Request $request, $practitionerId): JsonResponse
    {
        try {

            $practitioner = Practitioner::find($practitionerId);
            if (! $practitioner) {
                return response()->json(['message' => 'Médico no encontrado'], 404);
            }

            // Validate request parameters
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'date' => 'nullable|date|after_or_equal:today',
                'duration' => 'nullable|integer|min:15|max:480',
                'days' => 'nullable|integer|min:1|max:14', // Number of days to check
            ]);

            if(!$request->has('date')) $date = now();


            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $startDate = Carbon::parse($date);
            $duration = $request->get('duration', 30); // Default 30 minutes
            $daysToCheck = $request->get('days', 1); // Default 1 day

            $availability = [];

            for ($i = 0; $i < $daysToCheck; $i++) {
                $currentDate = $startDate->copy()->addDays($i);

                // Skip weekends if practitioner doesn't work weekends (this could be configurable)
                $dayOfWeek = $currentDate->dayOfWeek;

                $dayAvailability = [
                    'date' => $currentDate->format('Y-m-d'),
                    'day_name' => $this->getDayNameInSpanish($dayOfWeek),
                    'day_of_week' => $dayOfWeek,
                    'slots' => [],
                ];

                // Define working hours (this should ideally come from practitioner's schedule)
                $workingHours = $this->getPractitionerWorkingHours($practitioner, $dayOfWeek);

                if ($workingHours) {
                    $startTime = Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['start']);
                    $endTime = Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['end']);
                    $lunchStart = $workingHours['lunch_start'] ? Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['lunch_start']) : null;
                    $lunchEnd = $workingHours['lunch_end'] ? Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['lunch_end']) : null;

                    // Get existing appointments for this day
                    $existingAppointments = Appointment::where('practitioner_id', $practitioner->id)
                        ->whereDate('start', $currentDate->format('Y-m-d'))
                        ->whereNotIn('status', ['cancelled', 'noshow', 'entered-in-error'])
                        ->get(['start', 'end']);

                    // Generate time slots
                    $currentSlot = $startTime->copy();

                    while ($currentSlot->copy()->addMinutes($duration)->lte($endTime)) {
                        $slotEnd = $currentSlot->copy()->addMinutes($duration);

                        // Check if slot is during lunch break
                        $isDuringLunch = false;
                        if ($lunchStart && $lunchEnd) {
                            $isDuringLunch = ($currentSlot->lt($lunchEnd) && $slotEnd->gt($lunchStart));
                        }

                        // Check if slot conflicts with existing appointments
                        $hasConflict = false;
                        foreach ($existingAppointments as $appointment) {
                            $appointmentStart = Carbon::parse($appointment->start);
                            $appointmentEnd = Carbon::parse($appointment->end);

                            if (($currentSlot->lt($appointmentEnd) && $slotEnd->gt($appointmentStart))) {
                                $hasConflict = true;
                                break;
                            }
                        }

                        $dayAvailability['slots'][] = [
                            'time' => $currentSlot->format('H:i'),
                            'end_time' => $slotEnd->format('H:i'),
                            'available' => ! $hasConflict && ! $isDuringLunch && $currentSlot->gt(now()),
                            'reason' => $hasConflict ? 'Cita existente' : ($isDuringLunch ? 'Hora de almuerzo' : ($currentSlot->lte(now()) ? 'Hora pasada' : null)),
                        ];

                        $currentSlot->addMinutes($duration);
                    }
                } else {
                    // No working hours for this day
                    $dayAvailability['slots'] = [];
                    $dayAvailability['reason'] = 'Día no laborable';
                }

                $availability[] = $dayAvailability;
            }

            // Calculate summary statistics
            $totalSlots = 0;
            $availableSlots = 0;
            foreach ($availability as $day) {
                foreach ($day['slots'] as $slot) {
                    $totalSlots++;
                    if ($slot['available']) {
                        $availableSlots++;
                    }
                }
            }

            return response()->json([
                'practitioner' => [
                    'id' => $practitioner->id,
                    'name' => $practitioner->name,
                ],
                'duration_minutes' => $duration,
                'availability' => $availability,
                'summary' => [
                    'total_slots' => $totalSlots,
                    'available_slots' => $availableSlots,
                    'occupancy_rate' => $totalSlots > 0 ? round((($totalSlots - $availableSlots) / $totalSlots) * 100, 1) : 0,
                ],
            ]);
        }catch (\Exception$e){
            return response()->json(['error' => $e->getMessage()]);
        }

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

    /**
     * Obtener las fechas de la semana especificada
     */
    private function getWeekDates($weekStart): array
    {
        $dates = [];
        $current = $weekStart->copy();

        for ($i = 0; $i < 7; $i++) {
            $dates[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $current->format('l'),
                'day_name_es' => $this->getDayNameInSpanish($current->format('l')),
                'day_short' => $current->format('D'),
                'day_number' => $current->format('j'),
            ];
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Traducir nombres de días al español
     */
    private function getDayNameInSpanish(string $dayName): string
    {
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        return $days[$dayName] ?? $dayName;
    }
}
