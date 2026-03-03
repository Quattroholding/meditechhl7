<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceMessage;
use App\Models\Encounter;
use App\Models\Observation;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HemoScreenController extends Controller
{
    /**
     * Get ServiceRequest by hemo_identification
     *
     * Returns the service_request_id for a given hemo_identification number
     * This allows the device/operator to look up the correct ServiceRequest
     * before sending lab results
     */
    public function getServiceRequest(Request $request, string $hemo_identification)
    {
        $practitioner = $request->authenticated_practitioner;

        if (! $practitioner) {
            return response()->json([
                'error' => 'Token sin practitioner',
                'message' => 'Este endpoint requiere un token asociado a un practitioner',
            ], 403);
        }

        $practitioner->load('user');

        if (! $practitioner->user || ! $practitioner->user->default_client_id) {
            return response()->json([
                'error' => 'Configuración incompleta',
                'message' => 'El practitioner no tiene un usuario o cliente por defecto configurado',
            ], 500);
        }

        $clinicId = $practitioner->user->default_client_id;

        $serviceRequest = ServiceRequest::where('hemo_identification', $hemo_identification)
            ->where('practitioner_id', $practitioner->id)
            ->first();

        if (! $serviceRequest) {
            return response()->json([
                'error' => 'ServiceRequest no encontrado',
                'message' => "No se encontró un ServiceRequest con identificación HemoScreen {$hemo_identification} en la clínica {$clinicId}",
            ], 404);
        }

        $patient = Patient::find($serviceRequest->patient_id);

        return response()->json([
            'success' => true,
            'data' => [
                'service_request_id' => $serviceRequest->id,
                'hemo_identification' => $serviceRequest->hemo_identification,
                'patient_id' => $serviceRequest->patient_id,
                'patient_name' => $patient->name,
                'patient_birth_date' => $patient->birth_date,
                'patient_gender' => $patient->gender,
                'patient_identifier' => $serviceRequest->patient->identifier ?? null,
                'status' => $serviceRequest->status,
                'code' => $serviceRequest->code,
                'code_display' => $serviceRequest->code_display,
                'created_at' => $serviceRequest->created_at,
            ],
        ]);
    }

    public function __invoke(Request $request)
    {
        return DB::transaction(function () use ($request) {

            // Validar datos requeridos
            $validated = $request->validate([
                'control_id' => 'required|string',
                'message_type' => 'required|string',
                'device_serial' => 'nullable|string',
                'patient_identifier' => 'required|string',
                'observations' => 'required|array|min:1',
                'observations.*.loinc' => 'required|string',
                'observations.*.value' => 'required',
                'observations.*.unit' => 'required|string',
            ]);

            $patientIdentifier = $validated['patient_identifier'];

            // 🔎 1. Obtener practitioner y clinic_id del token autenticado
            $practitioner = $request->authenticated_practitioner;

            if (! $practitioner) {
                return response()->json([
                    'error' => 'Token sin practitioner',
                    'message' => 'Este endpoint requiere un token asociado a un practitioner',
                ], 403);
            }

            // Cargar usuario para obtener el default_client_id
            $practitioner->load('user');

            if (! $practitioner->user || ! $practitioner->user->default_client_id) {
                return response()->json([
                    'error' => 'Configuración incompleta',
                    'message' => 'El practitioner no tiene un usuario o cliente por defecto configurado',
                ], 500);
            }

            $clinicId = $practitioner->user->default_client_id;
            $practitionerId = $practitioner->id;

            $existing = DeviceMessage::where('clinic_id', $clinicId)
                ->where('message_control_id', $validated['control_id'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje ya procesado previamente (duplicado ignorado)',
                ]);
            }

            // 🔎 2. Búsqueda inteligente: primero por hemo_identification, luego por cédula
            $patient = null;
            $service_request = null;
            $foundByHemoId = false;

            // INTENTO 1: Buscar ServiceRequest por hemo_identification
            // El gateway puede enviar el código HemoScreen en patient_identifier
            if (strlen($patientIdentifier) === 7 && is_numeric($patientIdentifier)) {
                $service_request = ServiceRequest::where('hemo_identification', $patientIdentifier)
                    ->where('practitioner_id', $practitionerId)
                    ->whereNull('message_control_id')
                    ->whereIn('code', ['85025', '85027'])
                    ->where('status', '!=', 'completed')
                    ->with(['patient', 'encounter'])
                    ->first();

                if ($service_request) {
                    $patient = $service_request->patient;
                    $foundByHemoId = true;
                }
            }

            // INTENTO 2: Si no se encontró por hemo_identification, buscar paciente por cédula
            if (! $patient) {
                $patient = Patient::where('identifier', $patientIdentifier)
                    // ->where('scb_id', $clinicId)
                    ->first();

                if (! $patient) {
                    return response()->json([
                        'error' => 'Paciente no encontrado',
                        'message' => "No se encontró un paciente con identificador {$patientIdentifier} en la clínica {$clinicId}",
                    ], 404);
                }

                // Buscar ServiceRequest existente con CPT 85025/85027 sin message_control_id
                $service_request = ServiceRequest::where('patient_id', $patient->id)
                    ->where('scb_id', $clinicId)
                    ->whereNull('message_control_id')
                    ->whereIn('code', ['85025', '85027'])
                    ->where('status', '!=', 'completed')
                    ->orderBy('created_at', 'desc')
                    ->first();

            }

            DeviceMessage::create([
                'clinic_id' => $clinicId,
                'device_serial' => $request->device_serial,
                'message_control_id' => $validated['control_id'],
                'message_type' => $validated['message_type'],
            ]);

            // 🏥 3. Procesar ServiceRequest y Encounter
            $encounter = null;
            $foundExistingServiceRequest = false;
            $searchMethod = $foundByHemoId ? 'hemo_identification' : 'patient_identifier';

            if ($service_request) {
                // CASO 1: Usar el encounter del ServiceRequest existente
                $encounter = $service_request->encounter;
                $foundExistingServiceRequest = true;

                // Actualizar el ServiceRequest con el message_control_id y marcar como completado
                $service_request->update([
                    'message_control_id' => $validated['control_id'],
                    'status' => 'completed',
                    'last_updated' => now(),
                ]);
            } else {
                // CASO 2: Buscar un encounter del día de hoy del paciente
                $encounter = Encounter::where('patient_id', $patient->id)
                    ->where('scb_id', $clinicId)
                    ->whereDate('start', today())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('start', 'desc')
                    ->first();

                if ($encounter) {
                    // CASO 2: Crear el ServiceRequest en el encounter encontrado
                    $service_request = ServiceRequest::create([
                        'fhir_id' => 'servicerequest-'.Str::uuid(),
                        'encounter_id' => $encounter->id,
                        'patient_id' => $patient->id,
                        'practitioner_id' => $encounter->practitioner_id, // Usar practitioner del encounter
                        'message_control_id' => $validated['control_id'],
                        'status' => 'completed',
                        'intent' => 'order',
                        'priority' => 'routine',
                        'code' => '85025',
                        'service_type' => 'laboratory',
                        'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                        'code_display' => 'Complete Blood Count (CBC)',
                        'quantity' => 1,
                        'occurrence_start' => now(),
                        'authored_on' => now(),
                        'last_updated' => now(),
                        'scb_id' => $clinicId,
                    ]);
                } else {
                    // CASO 3: No hay nada, crear nuevo encounter
                    $uuid = Str::uuid();
                    $encounter = Encounter::create([
                        'fhir_id' => 'encounter-'.$uuid,
                        'identifier' => 'ENC-'.$uuid,
                        'patient_id' => $patient->id,
                        'practitioner_id' => $practitionerId,
                        'start' => now(),
                        'end' => now(),
                        'status' => 'finished',
                        'class' => 'AMB', // FHIR: Ambulatory
                        'type' => 'laboratory',
                        'scb_id' => $clinicId,
                    ]);

                    // Crear el ServiceRequest para el nuevo encounter
                    $service_request = ServiceRequest::create([
                        'fhir_id' => 'servicerequest-'.Str::uuid(),
                        'encounter_id' => $encounter->id,
                        'patient_id' => $patient->id,
                        'practitioner_id' => $practitionerId,
                        'message_control_id' => $validated['control_id'],
                        'status' => 'completed',
                        'intent' => 'order',
                        'priority' => 'routine',
                        'code' => '85025',
                        'service_type' => 'laboratory',
                        'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                        'code_display' => 'Complete Blood Count (CBC)',
                        'quantity' => 1,
                        'occurrence_start' => now(),
                        'authored_on' => now(),
                        'last_updated' => now(),
                        'scb_id' => $clinicId,
                    ]);
                }
            }

            // 🧪 4. Crear Observations asociadas al ServiceRequest y Encounter
            $createdObservations = [];
            foreach ($validated['observations'] as $obs) {
                $observation = Observation::create([
                    'fhir_id' => 'observation-'.Str::uuid(),
                    'patient_id' => $patient->id,
                    'encounter_id' => $encounter->id,
                    'practitioner_id' => $encounter->practitioner_id,
                    'service_request_id' => $service_request->id,
                    'status' => 'final',
                    'category' => 'laboratory',
                    'code' => $obs['loinc'],
                    'value' => $obs['value'],
                    'unit' => $obs['unit'],
                    'effective_date' => now(),
                    'issued_date' => now(),
                ]);

                $createdObservations[] = [
                    'id' => $observation->id,
                    'code' => $observation->code,
                    'value' => $observation->value,
                    'unit' => $observation->unit,
                ];
            }

            // Disparar evento para actualización en tiempo real
            broadcast(new \App\Events\LabResultsReceived($service_request))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Observaciones registradas exitosamente',
                'data' => [
                    'encounter_id' => $encounter->id,
                    'service_request_id' => $service_request->id,
                    'patient_id' => $patient->id,
                    'practitioner_id' => $encounter->practitioner_id,
                    'observations_count' => count($createdObservations),
                    'observations' => $createdObservations,
                    'found_existing_service_request' => $foundExistingServiceRequest,
                    'search_method' => $searchMethod,
                ],
            ]);
        });
    }
}
