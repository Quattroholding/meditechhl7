<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\ConsultingRoom;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\Practitioner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        // Get pagination parameters with default of 10 per page
        $perPage = $request->input('per_page', 10);
        $perPage = min(max((int) $perPage, 1), 100); // Limit between 1 and 100

        $appointmentsQuery = Appointment::where('patient_id', $patient->id)
            ->with(['practitioner', 'medicalSpeciality', 'consultingRoom'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                return $query->whereDate('start', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                return $query->whereDate('start', '<=', $dateTo);
            })
            ->orderBy('start', 'desc');

        $appointments = $appointmentsQuery->paginate($perPage);

        $appointmentData = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'description' => $appointment->description,
                'start' => $appointment->start->format('Y-m-d H:i:s'),
                'end' => $appointment->end->format('Y-m-d H:i:s'),
                'formatted_date' => $appointment->formatted_date,
                'formatted_time' => $appointment->formatted_time,
                'minutes_duration' => $appointment->minutes_duration,
                'practitioner' => [
                    'id' => $appointment->practitioner->id,
                    'name' => $appointment->practitioner->name,
                    'speciality' => $appointment->medicalSpeciality->name ?? 'Medicina General',
                ],
                'consulting_room' => [
                    'id' => $appointment->consultingRoom->id ?? null,
                    'name' => $appointment->consultingRoom->name ?? 'N/A',
                ],
                'can_cancel' => $this->canCancelAppointment($appointment),
                'can_reschedule' => $this->canRescheduleAppointment($appointment),
            ];
        });

        return response()->json([
            'data' => $appointmentData,
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
                'last_page' => $appointments->lastPage(),
                'from' => $appointments->firstItem(),
                'to' => $appointments->lastItem(),
                'has_more_pages' => $appointments->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Direct database token lookup
            $bearerToken = $request->bearerToken();
            if (! $bearerToken) {
                return response()->json(['message' => 'Token no proporcionado'], 401);
            }

            // Parse Sanctum token format: ID|TOKEN
            $tokenParts = explode('|', $bearerToken, 2);
            if (count($tokenParts) !== 2) {
                return response()->json(['message' => 'Formato de token inválido'], 401);
            }

            $tokenId = $tokenParts[0];
            $plainTextToken = $tokenParts[1];

            // Raw database query to avoid any authentication middleware
            $tokenRecord = DB::table('personal_access_tokens')
                ->where('id', $tokenId)
                ->where('token', hash('sha256', $plainTextToken))
                ->first();

            if (! $tokenRecord) {
                return response()->json(['message' => 'Token inválido'], 401);
            }

            // Get user directly from database
            $user = User::where('id', $tokenRecord->tokenable_id)->first();
            if (! $user) {
                return response()->json(['message' => 'Usuario no encontrado'], 401);
            }

            // Basic validation
            if (! $request->practitioner_id || ! $request->start || ! $request->minutes_duration) {
                return response()->json(['message' => 'Faltan campos requeridos'], 400);
            }

            // Get patient directly from database
            $patient = Patient::where('user_id', $user->id)->first();
            if (! $patient) {
                return response()->json(['message' => 'Paciente no encontrado'], 404);
            }

            // Get practitioner
            $practitioner = Practitioner::where('id', $request->practitioner_id)->first();
            if (! $practitioner) {
                return response()->json(['message' => 'Médico no encontrado'], 404);
            }

            $startTime = Carbon::parse($request->start);
            $endTime = $startTime->copy()->addMinutes((int) $request->minutes_duration);

            // Check conflicts
            $conflicts = Appointment::where('practitioner_id', $request->practitioner_id)
                ->whereNotIn('status',['cancelled','noshow','entered-in-error'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start', [$startTime, $endTime])
                        ->orWhereBetween('end', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start', '<=', $startTime)
                                ->where('end', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'message' => 'El horario seleccionado no está disponible',
                ], 422);
            }

            $rooms = ConsultingRoom::find($request->consulting_room_id);
            $client_id = $rooms->branch->client_id;

            // Create appointment without global scope to avoid auth issues
            $appointmentId = DB::table('appointments')->insertGetId([
                'fhir_id' => 'appointment-'.Str::uuid(),
                'identifier' => 'APT-'.mt_rand(1000000, 9999999),
                'patient_id' => $patient->id,
                'practitioner_id' => $request->practitioner_id,
                'client_id' => $client_id,
                'status' => 'proposed',
                'service_type' => $request->service_type,
                'description' => $request->description,
                'start' => $startTime,
                'end' => $endTime,
                'minutes_duration' => (int) $request->minutes_duration,
                'medical_speciality_id' => $request->medical_speciality_id,
                'consulting_room_id' => $request->consulting_room_id,
                'original_requested_datetime' => $startTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $app = Appointment::find($appointmentId);

            if ($app) {
                PatientClient::create([
                    'patient_id' => $patient->id,
                    'client_id' => $client_id,
                ]);
                AppointmentStatus::create([
                    'appointment_id' => $app->id,
                    'status' => $app->status,
                    'user_id' => $user->id, // Asume que estás usando autenticación
                ]);
                $app->addPatientToPractitionerClient();
                $app->notifyPractitionerAboutProposal();
            }

            return response()->json([
                'message' => 'Cita creada exitosamente',
                'appointment' => [
                    'id' => $appointmentId,
                    'status' => 'proposed',
                    'service_type' => $request->service_type,
                    'description' => $request->description,
                    'start' => $startTime->format('Y-m-d H:i:s'),
                    'end' => $endTime->format('Y-m-d H:i:s'),
                    'practitioner' => [
                        'name' => $practitioner->name,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->with(['practitioner', 'medicalSpeciality', 'consultingRoom'])
            ->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json([
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'description' => $appointment->description,
                'start' => $appointment->start->format('Y-m-d H:i:s'),
                'end' => $appointment->end->format('Y-m-d H:i:s'),
                'formatted_date' => $appointment->formatted_date,
                'formatted_time' => $appointment->formatted_time,
                'minutes_duration' => $appointment->minutes_duration,
                'practitioner' => [
                    'id' => $appointment->practitioner->id,
                    'name' => $appointment->practitioner->name,
                    'speciality' => $appointment->medicalSpeciality->name ?? 'Medicina General',
                ],
                'consulting_room' => [
                    'id' => $appointment->consultingRoom->id ?? null,
                    'name' => $appointment->consultingRoom->name ?? 'N/A',
                ],
                'can_cancel' => $this->canCancelAppointment($appointment),
                'can_reschedule' => $this->canRescheduleAppointment($appointment),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (! $this->canRescheduleAppointment($appointment)) {
            return response()->json([
                'message' => 'No se puede modificar esta cita',
            ], 422);
        }

        $request->validate([
            'start' => 'required|date|after:now',
            'minutes_duration' => 'required|integer|min:15|max:180',
            'description' => 'nullable|string|max:500',
        ]);

        $startTime = Carbon::parse($request->start);
        $endTime = $startTime->copy()->addMinutes($request->minutes_duration);

        // Verificar disponibilidad (excluyendo la cita actual)
        $conflictingAppointment = Appointment::where('practitioner_id', $appointment->practitioner_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start', [$startTime, $endTime])
                    ->orWhereBetween('end', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start', '<=', $startTime)
                            ->where('end', '>=', $endTime);
                    });
            })
            ->exists();

        if ($conflictingAppointment) {
            return response()->json([
                'message' => 'El horario seleccionado no está disponible',
                'errors' => [
                    'start' => ['El médico ya tiene una cita en ese horario'],
                ],
            ], 422);
        }

        $appointment->update([
            'start' => $startTime,
            'end' => $endTime,
            'minutes_duration' => $request->minutes_duration,
            'description' => $request->description,
            'status' => 'proposed', // Vuelve a propuesta para aprobación
        ]);

        // Notificar al médico sobre el cambio
        $appointment->notifyPractitionerAboutProposal();

        return response()->json([
            'message' => 'Cita actualizada exitosamente',
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'start' => $appointment->start->format('Y-m-d H:i:s'),
                'end' => $appointment->end->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (! $this->canCancelAppointment($appointment)) {
            return response()->json([
                'message' => 'No se puede cancelar esta cita',
            ], 422);
        }

        $appointment->update(['status' => 'cancelled']);

        // Notificar al médico sobre la cancelación
        if (in_array($appointment->status, ['booked', 'confirmed'])) {
            $appointment->notifyPatientAboutCancellation('Cancelada por el paciente');
        }

        return response()->json([
            'message' => 'Cita cancelada exitosamente',
        ]);
    }

    public function checkAvailability(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json([
            'can_cancel' => $this->canCancelAppointment($appointment),
            'can_reschedule' => $this->canRescheduleAppointment($appointment),
            'status' => $appointment->status,
        ]);
    }

    private function canCancelAppointment($appointment)
    {
        // No se puede cancelar si ya está cancelada, finalizada o cumplida
        if (in_array($appointment->status, ['cancelled', 'fulfilled', 'noshow'])) {
            return false;
        }

        // No se puede cancelar si es en menos de 2 horas
        $hoursUntilAppointment = now()->diffInHours($appointment->start, false);

        return $hoursUntilAppointment >= 2;
    }

    private function canRescheduleAppointment($appointment)
    {
        // Solo se puede reprogramar si está en propuesta o confirmada
        if (! in_array($appointment->status, ['proposed', 'booked'])) {
            return false;
        }

        // No se puede reprogramar si es en menos de 24 horas
        $hoursUntilAppointment = now()->diffInHours($appointment->start, false);

        return $hoursUntilAppointment >= 24;
    }

    // API v1 Methods for external integrations

    /**
     * List all appointments for API v1
     */
    public function indexV1(Request $request)
    {
        $query = Appointment::with(['patient', 'practitioner', 'medicalSpeciality', 'consultingRoom']);

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('identifier', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('practitioner', function($practitionerQuery) use ($search) {
                    $practitionerQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('identifier', 'like', "%{$search}%");
                })
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by patient ID
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by practitioner ID
        if ($request->has('practitioner_id')) {
            $query->where('practitioner_id', $request->practitioner_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('start', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('start', '<=', $request->date_to);
        }

        // Filter by medical speciality
        if ($request->has('medical_speciality_id')) {
            $query->where('medical_speciality_id', $request->medical_speciality_id);
        }

        // Ordering
        $orderBy = $request->get('order_by', 'start');
        $orderDirection = $request->get('order_direction', 'desc');

        $allowedOrderColumns = ['start', 'end', 'created_at', 'updated_at', 'status'];
        if (in_array($orderBy, $allowedOrderColumns)) {
            $query->orderBy($orderBy, $orderDirection);
        }

        // Pagination
        $perPage = min(max((int)$request->get('per_page', 15), 1), 100);
        $appointments = $query->paginate($perPage);

        return response()->json([
            'data' => $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'fhir_id' => $appointment->fhir_id,
                    'identifier' => $appointment->identifier,
                    'status' => $appointment->status,
                    'description' => $appointment->description,
                    'start' => $appointment->start,
                    'end' => $appointment->end,
                    'minutes_duration' => $appointment->minutes_duration,
                    'formatted_date' => $appointment->formatted_date,
                    'formatted_time' => $appointment->formatted_time,
                    'patient' => [
                        'id' => $appointment->patient->id,
                        'name' => $appointment->patient->name,
                        'identifier' => $appointment->patient->identifier,
                        'email' => $appointment->patient->email,
                        'phone' => $appointment->patient->phone,
                    ],
                    'practitioner' => [
                        'id' => $appointment->practitioner->id,
                        'name' => $appointment->practitioner->name,
                        'identifier' => $appointment->practitioner->identifier,
                    ],
                    'medical_speciality' => $appointment->medicalSpeciality ? [
                        'id' => $appointment->medicalSpeciality->id,
                        'name' => $appointment->medicalSpeciality->name,
                    ] : null,
                    'consulting_room' => $appointment->consultingRoom ? [
                        'id' => $appointment->consultingRoom->id,
                        'name' => $appointment->consultingRoom->name,
                        'location' => $appointment->consultingRoom->location,
                    ] : null,
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
                'last_page' => $appointments->lastPage(),
                'from' => $appointments->firstItem(),
                'to' => $appointments->lastItem(),
                'has_more_pages' => $appointments->hasMorePages(),
            ],
            'links' => [
                'first' => $appointments->url(1),
                'last' => $appointments->url($appointments->lastPage()),
                'prev' => $appointments->previousPageUrl(),
                'next' => $appointments->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Create a new appointment for API v1
     */
    public function storeV1(Request $request)
    {
        // Validate required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'practitioner_id' => 'required|exists:practitioners,id',
            'start' => 'required|date|after:now',
            'minutes_duration' => 'required|integer|min:15|max:480',
            'service_type' => 'required|string|max:1000',
            'medical_speciality_id' => 'required|exists:medical_specialties,id',
            'description' => 'nullable|string|max:1000',
            'consulting_room_id' => 'nullable|exists:consulting_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $startTime = Carbon::parse($request->start);
            $endTime = $startTime->copy()->addMinutes((int) $request->minutes_duration);

            // Check for conflicts
            $conflicts = Appointment::where('practitioner_id', $request->practitioner_id)
                ->whereNotIn('status',['cancelled','noshow','entered-in-error'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start', [$startTime, $endTime])
                        ->orWhereBetween('end', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start', '<=', $startTime)
                              ->where('end', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'message' => 'El horario solicitado no está disponible',
                ], 409);
            }

            $practitioner = Practitioner::find($request->practitioner_id);
            $client_id = $practitioner->user->default_client_id;


            // Create appointment
            $appointment = Appointment::create([
                'fhir_id' => 'appointment-'.Str::uuid(),
                'identifier' => 'APT-'.fake()->unique()->numerify('#######'),
                'patient_id' => $request->patient_id,
                'practitioner_id' => $request->practitioner_id,
                'start' => $startTime,
                'end' => $endTime,
                'status' => 'booked',
                'service_type'=>$request->service_type,
                'description' => $request->description,
                'minutes_duration' => $request->minutes_duration,
                'medical_speciality_id' => $request->medical_speciality_id,
                'consulting_room_id' => $request->consulting_room_id,
                'original_requested_datetime' => $startTime,
                'client_id'=> $client_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($appointment) {
                AppointmentStatus::create([
                    'appointment_id' => $appointment->id,
                    'status' => $appointment->status,
                    'user_id' => 1, // Asume que estás usando autenticación
                ]);
                $appointment->addPatientToPractitionerClient();
                $appointment->notifyPatientAboutConfirmation();
            }

            // Load relationships
            $appointment->load(['patient', 'practitioner', 'medicalSpeciality', 'consultingRoom']);

            return response()->json([
                'message' => 'Cita creada exitosamente',
                'data' => [
                    'id' => $appointment->id,
                    'fhir_id' => $appointment->fhir_id,
                    'identifier' => $appointment->identifier,
                    'status' => $appointment->status,
                    'service_type' => $appointment->service_type,
                    'description' => $appointment->description,
                    'start' => $appointment->start,
                    'end' => $appointment->end,
                    'minutes_duration' => $appointment->minutes_duration,
                    'patient' => [
                        'id' => $appointment->patient->id,
                        'name' => $appointment->patient->name,
                        'identifier' => $appointment->patient->identifier,
                    ],
                    'practitioner' => [
                        'id' => $appointment->practitioner->id,
                        'name' => $appointment->practitioner->name,
                        'identifier' => $appointment->practitioner->identifier,
                    ],
                    'medical_speciality' => $appointment->medicalSpeciality ? [
                        'id' => $appointment->medicalSpeciality->id,
                        'name' => $appointment->medicalSpeciality->name,
                    ] : null,
                    'consulting_room' => $appointment->consultingRoom ? [
                        'id' => $appointment->consultingRoom->id,
                        'name' => $appointment->consultingRoom->name,
                    ] : null,
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->updated_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creando la cita',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an appointment for API v1
     */
    public function updateV1(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        // Validate fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'patient_id' => 'sometimes|exists:patients,id',
            'practitioner_id' => 'sometimes|exists:practitioners,id',
            'start' => 'sometimes|date|after:now',
            'minutes_duration' => 'sometimes|integer|min:15|max:480',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:requested,confirmed,booked,arrived,fulfilled,cancelled,no_show',
            'medical_speciality_id' => 'nullable|exists:medical_specialities,id',
            'consulting_room_id' => 'nullable|exists:consulting_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check for scheduling conflicts if date/time is being changed
            if ($request->has('start') || $request->has('minutes_duration') || $request->has('practitioner_id')) {
                $startTime = $request->has('start') ? Carbon::parse($request->start) : $appointment->start;
                $duration = $request->has('minutes_duration') ? (int) $request->minutes_duration : $appointment->minutes_duration;
                $practitionerId = $request->has('practitioner_id') ? $request->practitioner_id : $appointment->practitioner_id;
                $endTime = $startTime->copy()->addMinutes($duration);

                $conflicts = Appointment::where('practitioner_id', $practitionerId)
                    ->where('id', '!=', $appointment->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->whereBetween('start', [$startTime, $endTime])
                            ->orWhereBetween('end', [$startTime, $endTime])
                            ->orWhere(function ($q) use ($startTime, $endTime) {
                                $q->where('start', '<=', $startTime)
                                  ->where('end', '>=', $endTime);
                            });
                    })
                    ->exists();

                if ($conflicts) {
                    return response()->json([
                        'message' => 'El horario solicitado no está disponible',
                    ], 409);
                }

                // Update end time if start or duration changed
                if ($request->has('start') || $request->has('minutes_duration')) {
                    $appointment->end = $endTime;
                }
            }

            // Update allowed fields
            $fillableFields = ['patient_id', 'practitioner_id', 'start', 'minutes_duration', 'description', 'status', 'medical_speciality_id', 'consulting_room_id'];
            foreach ($fillableFields as $field) {
                if ($request->has($field)) {
                    $appointment->$field = $request->$field;
                }
            }

            $appointment->save();

            // Load relationships
            $appointment->load(['patient', 'practitioner', 'medicalSpeciality', 'consultingRoom']);

            return response()->json([
                'message' => 'Cita actualizada exitosamente',
                'data' => [
                    'id' => $appointment->id,
                    'fhir_id' => $appointment->fhir_id,
                    'identifier' => $appointment->identifier,
                    'status' => $appointment->status,
                    'description' => $appointment->description,
                    'start' => $appointment->start,
                    'end' => $appointment->end,
                    'minutes_duration' => $appointment->minutes_duration,
                    'patient' => [
                        'id' => $appointment->patient->id,
                        'name' => $appointment->patient->name,
                        'identifier' => $appointment->patient->identifier,
                    ],
                    'practitioner' => [
                        'id' => $appointment->practitioner->id,
                        'name' => $appointment->practitioner->name,
                        'identifier' => $appointment->practitioner->identifier,
                    ],
                    'medical_speciality' => $appointment->medicalSpeciality ? [
                        'id' => $appointment->medicalSpeciality->id,
                        'name' => $appointment->medicalSpeciality->name,
                    ] : null,
                    'consulting_room' => $appointment->consultingRoom ? [
                        'id' => $appointment->consultingRoom->id,
                        'name' => $appointment->consultingRoom->name,
                    ] : null,
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->updated_at,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error actualizando la cita',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific appointment for API v1
     */
    public function showV1($id)
    {
        $appointment = Appointment::with(['patient', 'practitioner', 'medicalSpeciality', 'consultingRoom'])
                                 ->find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $appointment->id,
                'fhir_id' => $appointment->fhir_id,
                'identifier' => $appointment->identifier,
                'status' => $appointment->status,
                'description' => $appointment->description,
                'start' => $appointment->start,
                'end' => $appointment->end,
                'minutes_duration' => $appointment->minutes_duration,
                'formatted_date' => $appointment->formatted_date,
                'formatted_time' => $appointment->formatted_time,
                'patient' => [
                    'id' => $appointment->patient->id,
                    'name' => $appointment->patient->name,
                    'identifier' => $appointment->patient->identifier,
                    'email' => $appointment->patient->email,
                    'phone' => $appointment->patient->phone,
                ],
                'practitioner' => [
                    'id' => $appointment->practitioner->id,
                    'name' => $appointment->practitioner->name,
                    'identifier' => $appointment->practitioner->identifier,
                ],
                'medical_speciality' => $appointment->medicalSpeciality ? [
                    'id' => $appointment->medicalSpeciality->id,
                    'name' => $appointment->medicalSpeciality->name,
                ] : null,
                'consulting_room' => $appointment->consultingRoom ? [
                    'id' => $appointment->consultingRoom->id,
                    'name' => $appointment->consultingRoom->name,
                    'location' => $appointment->consultingRoom->location,
                ] : null,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->updated_at,
            ],
        ]);
    }

    public function checkAvailabilityV1(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        return response()->json([
            'can_cancel' => $this->canCancelAppointment($appointment),
            'can_reschedule' => $this->canRescheduleAppointment($appointment),
            'status' => $appointment->status,
        ]);
    }

    public function destroyV1(Request $request, $id)
    {

        $appointment = Appointment::where('id', $id)->first();

        if (! $appointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        if (! $this->canCancelAppointment($appointment)) {
            return response()->json([
                'message' => 'No se puede cancelar esta cita',
            ], 422);
        }

        $appointment->update(['status' => 'cancelled']);

        // Notificar al médico sobre la cancelación
        if (in_array($appointment->status, ['booked', 'confirmed'])) {
            $appointment->notifyPatientAboutCancellation('Cancelada por el paciente');
        }

        return response()->json([
            'message' => 'Cita cancelada exitosamente',
        ]);
    }
}
