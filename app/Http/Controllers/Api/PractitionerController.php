<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PractitionerResource;
use App\Models\Appointment;
use App\Models\Practitioner;
use App\Models\ServiceCatalog;
use App\Models\UserWorkingHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default 10 items per page
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $practitioners = Practitioner::with(['specialties', 'user.clients', 'insuranceCompanies'])
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
            ->userActive()
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
            'next_week_info' => [
                'start_date' => $nextWeekStart->format('Y-m-d'),
                'end_date' => $nextWeekEnd->format('Y-m-d'),
                'week_dates' => $this->getWeekDates($nextWeekStart),
            ],
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

    public function availability(Request $request, $practitionerId): JsonResponse
    {
        try {

            $practitioner = Practitioner::with('user')->find($practitionerId);
            if (! $practitioner) {
                return response()->json(['message' => 'Médico no encontrado'], 404);
            }

            // Validate request parameters
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'date' => 'nullable|date|after_or_equal:today',
                'duration' => 'nullable|integer|min:15|max:480',
                'days' => 'nullable|integer|min:1|max:14', // Number of days to check
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $date = $request->get('date', now()->format('Y-m-d'));
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
        } catch (\Exception$e) {
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
     * Get day name in Spanish
     * Accepts both numeric day (0-6) and English day name string
     */
    private function getDayNameInSpanish($dayOfWeek)
    {
        // Handle numeric days
        $numericDays = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];

        // Handle string day names
        $stringDays = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        // If it's numeric, use numeric array
        if (is_numeric($dayOfWeek)) {
            return $numericDays[$dayOfWeek] ?? 'Desconocido';
        }

        // If it's a string, use string array
        return $stringDays[$dayOfWeek] ?? 'Desconocido';
    }

    /**
     * Get practitioner working hours for a specific day
     * Checks user_working_hours table first, falls back to defaults
     */
    private function getPractitionerWorkingHours($practitioner, $dayOfWeek)
    {
        // Convert numeric day to string format
        $dayNames = [
            0 => 'sunday',
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
        ];

        $dayName = $dayNames[$dayOfWeek];

        // Check if practitioner has a user and working hours configured
        if ($practitioner->user) {
            $workingHour = UserWorkingHour::where('user_id', $practitioner->user->id)
                ->where('day_of_week', $dayName)
                ->whereNull('deleted_at')
                ->first();

            if ($workingHour) {
                return [
                    'start' => $workingHour->start_time,
                    'end' => $workingHour->end_time,
                    'lunch_start' => '12:00', // Default lunch time since it's not in the table
                    'lunch_end' => '13:00',   // Default lunch time since it's not in the table
                ];
            }
        }

        // Skip weekends by default if no custom schedule
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return null;
        }

        // Default working hours
        return [
            'start' => '08:00',
            'end' => '17:00',
            'lunch_start' => '12:00',
            'lunch_end' => '13:00',
        ];
    }

    /**
     * Get next available slots for a practitioner
     * Returns the next 5 available time slots starting from now + 2 hours
     */
    public function nextAvailableSlots(Request $request, $practitionerId): JsonResponse
    {
        try {
            $practitioner = Practitioner::with('user')->find($practitionerId);
            if (! $practitioner) {
                return response()->json(['message' => 'Médico no encontrado'], 404);
            }

            // Validate request parameters
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'duration' => 'nullable|integer|min:15|max:480',
                'max_slots' => 'nullable|integer|min:1|max:20',
                'max_days' => 'nullable|integer|min:1|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Parámetros de validación incorrectos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $duration = (int) $request->get('duration', 30); // Default 30 minutes
            $maxSlots = (int) $request->get('max_slots', 5); // Default 5 slots
            $maxDays = (int) $request->get('max_days', 14); // Search up to 14 days ahead

            // Start searching from now + 2 hours
            $searchStartTime = now()->addHours(2);

            // Round to next multiple of 5 minutes
            $searchStartTime = $this->roundToNextFiveMinutes($searchStartTime);

            $searchStartDate = $searchStartTime->copy()->startOfDay();

            $availableSlots = [];
            $daysChecked = 0;

            // Search until we find enough slots or reach max days
            while (count($availableSlots) < $maxSlots && $daysChecked < $maxDays) {
                $currentDate = $searchStartDate->copy()->addDays($daysChecked);
                $dayOfWeek = $currentDate->dayOfWeek;

                // Get working hours for this day
                $workingHours = $this->getPractitionerWorkingHours($practitioner, $dayOfWeek);

                if ($workingHours) {
                    $startTime = Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['start']);
                    $endTime = Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['end']);
                    $lunchStart = $workingHours['lunch_start'] ? Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['lunch_start']) : null;
                    $lunchEnd = $workingHours['lunch_end'] ? Carbon::parse($currentDate->format('Y-m-d').' '.$workingHours['lunch_end']) : null;

                    // If it's today, adjust start time to be at least searchStartTime
                    if ($currentDate->isToday()) {
                        if ($searchStartTime->gt($startTime)) {
                            $startTime = $searchStartTime->copy();
                        }
                    }

                    // Round start time to next multiple of 5 minutes
                    $startTime = $this->roundToNextFiveMinutes($startTime);

                    // Get existing appointments for this day
                    $existingAppointments = Appointment::where('practitioner_id', $practitioner->id)
                        ->whereDate('start', $currentDate->format('Y-m-d'))
                        ->whereNotIn('status', ['cancelled', 'noshow', 'entered-in-error'])
                        ->get(['start', 'end']);

                    // Generate time slots for this day
                    $currentSlot = $startTime->copy();

                    while ($currentSlot->copy()->addMinutes($duration)->lte($endTime) && count($availableSlots) < $maxSlots) {
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

                        // Check if slot is in the future
                        $isInFuture = $currentSlot->gt(now());

                        // If slot is available, add it to results
                        if (! $hasConflict && ! $isDuringLunch && $isInFuture) {
                            $availableSlots[] = [
                                'date' => $currentSlot->format('Y-m-d'),
                                'start_time' => $currentSlot->format('H:i'),
                                'end_time' => $slotEnd->format('H:i'),
                                'datetime' => $currentSlot->format('Y-m-d H:i:s'),
                                'day_name' => $this->getDayNameInSpanish($currentSlot->dayOfWeek),
                                'day_of_week' => $currentSlot->dayOfWeek,
                                'is_today' => $currentSlot->isToday(),
                                'is_tomorrow' => $currentSlot->isTomorrow(),
                                'human_readable' => $currentSlot->format('l, F j, Y \a\t g:i A'),
                                'human_readable_es' => $this->getDayNameInSpanish($currentSlot->dayOfWeek).', '.$currentSlot->format('j \d\e F Y \a \l\a\s H:i'),
                            ];
                        }

                        $currentSlot->addMinutes($duration);
                    }
                }

                $daysChecked++;
            }

            return response()->json([
                'practitioner' => [
                    'id' => $practitioner->id,
                    'name' => $practitioner->name,
                    'email' => $practitioner->email,
                    'phone' => $practitioner->phone,
                ],
                'search_params' => [
                    'duration_minutes' => $duration,
                    'max_slots_requested' => $maxSlots,
                    'search_start_time' => $searchStartTime->format('Y-m-d H:i:s'),
                    'days_searched' => $daysChecked,
                ],
                'available_slots' => $availableSlots,
                'total_slots_found' => count($availableSlots),
                'has_availability' => count($availableSlots) > 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los slots disponibles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Round time to the next multiple of 5 minutes
     * Example: 10:07 -> 10:10, 10:43 -> 10:45, 10:00 -> 10:00
     */
    private function roundToNextFiveMinutes(Carbon $time): Carbon
    {
        $minutes = $time->minute;
        $remainder = $minutes % 5;

        if ($remainder === 0) {
            // Already on a 5-minute mark
            return $time;
        }

        // Round up to next 5-minute mark
        $minutesToAdd = 5 - $remainder;

        return $time->copy()->addMinutes($minutesToAdd)->second(0);
    }

    /**
     * Get service catalog for a specific practitioner
     */
    public function serviceCatalog(Request $request, $practitionerId): JsonResponse
    {
        try {
            $practitioner = Practitioner::find($practitionerId);
            if (! $practitioner) {
                return response()->json(['message' => 'Médico no encontrado'], 404);
            }

            // Build query for service catalog
            $query = ServiceCatalog::active()->wherePractitionerId($practitionerId);

            // Apply filters
            if ($request->has('service_type')) {
                $query->byServiceType($request->service_type);
            }

            if ($request->has('specialty')) {
                $query->bySpecialty($request->specialty);
            }

            if ($request->has('cpt_code')) {
                $query->byCptCode($request->cpt_code);
            }

            if ($request->has('complexity')) {
                $query->byComplexity($request->complexity);
            }

            if ($request->has('covered_by_insurance')) {
                $query->coveredByInsurance();
            }

            if ($request->has('requires_authorization')) {
                $query->requiresAuthorization();
            }

            if ($request->has('min_price') && $request->has('max_price')) {
                $query->byPriceRange($request->min_price, $request->max_price);
            }

            if ($request->has('search')) {
                $searchTerm = trim($request->search);
                if (! empty($searchTerm)) {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%'.strtolower($searchTerm).'%'])
                            ->orWhere('code', 'like', "%$searchTerm%")
                            ->orWhere('cpt_code', 'like', "%$searchTerm%")
                            ->orWhere('hcpcs_code', 'like', "%$searchTerm%")
                            ->orWhere('icd10_pcs_code', 'like', "%$searchTerm%")
                            ->orWhere('snomed_code', 'like', "%$searchTerm%");
                    });
                }
            }

            // Pagination
            $perPage = min(max($request->input('per_page', 20), 1), 100);
            $services = $query->with(['client', 'practitioner'])
                ->orderBy('name')
                ->paginate($perPage);

            // Transform data for API response
            $servicesData = $services->getCollection()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'fhir_id' => $service->fhir_id,
                    'code' => $service->code,
                    'cpt_code' => $service->cpt_code,
                    'hcpcs_code' => $service->hcpcs_code,
                    'icd10_pcs_code' => $service->icd10_pcs_code,
                    'snomed_code' => $service->snomed_code,
                    'name' => $service->name,
                    'description' => $service->description,
                    'service_type' => $service->service_type,
                    'base_price' => $service->base_price,
                    'effective_price' => $service->effective_price,
                    'currency' => $service->currency,
                    'billing_unit' => $service->billing_unit,
                    'duration_minutes' => $service->duration_minutes,
                    'estimated_duration_hours' => $service->estimated_duration_hours,
                    'specialty' => $service->specialty,
                    'body_system' => $service->body_system,
                    'complexity' => $service->complexity,
                    'requires_authorization' => $service->requires_authorization,
                    'covered_by_insurance' => $service->covered_by_insurance,
                    'patient_copay' => $service->patient_copay,
                    'insurance_allowable' => $service->insurance_allowable,
                    'revenue_code' => $service->revenue_code,
                    'cost_center' => $service->cost_center,
                    'gl_account' => $service->gl_account,
                    'cost_estimate' => $service->cost_estimate,
                    'profit_margin' => $service->profit_margin,
                    'profit_margin_percentage' => $service->profit_margin_percentage,
                    'markup_percentage' => $service->markup_percentage,
                    'is_active' => $service->is_active,
                    'is_currently_available' => $service->is_currently_available,
                    'effective_date' => $service->effective_date?->format('Y-m-d'),
                    'expiration_date' => $service->expiration_date?->format('Y-m-d'),
                    'available_locations' => $service->available_locations,
                    'qualified_practitioners' => $service->qualified_practitioners,
                    'insurance_codes' => $service->insurance_codes,
                    'coding_systems' => $service->coding_systems,
                    'related_services' => $service->related_services,
                    'clinical_notes' => $service->clinical_notes,
                    'prerequisites' => $service->prerequisites,
                    'client' => $service->client ? [
                        'id' => $service->client->id,
                        'name' => $service->client->name,
                        'code' => $service->client->code,
                    ] : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ];
            });

            return response()->json([
                'practitioner' => [
                    'id' => $practitioner->id,
                    'name' => $practitioner->name,
                ],
                'data' => $servicesData,
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'last_page' => $services->lastPage(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                    'has_more_pages' => $services->hasMorePages(),
                ],
                'filters_applied' => [
                    'service_type' => $request->service_type,
                    'specialty' => $request->specialty,
                    'cpt_code' => $request->cpt_code,
                    'complexity' => $request->complexity,
                    'covered_by_insurance' => $request->boolean('covered_by_insurance'),
                    'requires_authorization' => $request->boolean('requires_authorization'),
                    'price_range' => $request->has('min_price') && $request->has('max_price') ? [
                        'min' => $request->min_price,
                        'max' => $request->max_price,
                    ] : null,
                    'search' => $request->search,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el catálogo de servicios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
