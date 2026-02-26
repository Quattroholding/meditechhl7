<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Encounter;
use App\Models\Observation;
use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceMessage;

class HemoScreenController extends Controller
{
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

            // 🔎 2. Buscar paciente por cédula
            $patient = Patient::where('identifier', $patientIdentifier)
                ->where('scb_id', $clinicId)
                ->first();

            if (! $patient) {
                return response()->json([
                    'error' => 'Paciente no encontrado',
                    'message' => "No se encontró un paciente con identificador {$patientIdentifier} en la clínica {$clinicId}",
                ], 404);
            }

            DeviceMessage::create([
                'clinic_id' => $clinicId,
                'device_serial' => $request->device_serial,
                'message_control_id' => $validated['control_id'],
                'message_type' => $validated['message_type'],
            ]);

            // 🏥 3. Crear Encounter automáticamente
            $uuid = \Illuminate\Support\Str::uuid();
            $encounter = Encounter::create([
                'fhir_id' => 'encounter-' . $uuid,
                'identifier' => 'ENC-' . $uuid,
                'patient_id' => $patient->id,
                'practitioner_id' => $practitionerId,
                'start' => now(),
                'end' => now(),
                'status' => 'finished',
                'class' => 'AMB', // FHIR: Ambulatory
                'type' => 'laboratory',
                'scb_id' => $clinicId,
            ]);

            // 🧪 4. Crear Observations
            $createdObservations = [];
            foreach ($validated['observations'] as $obs) {
                $observation = Observation::create([
                    'fhir_id' => 'observation-' . \Illuminate\Support\Str::uuid(),
                    'patient_id' => $patient->id,
                    'encounter_id' => $encounter->id,
                    'practitioner_id' => $practitionerId,
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

            return response()->json([
                'success' => true,
                'message' => 'Observaciones registradas exitosamente',
                'data' => [
                    'encounter_id' => $encounter->id,
                    'patient_id' => $patient->id,
                    'practitioner_id' => $practitionerId,
                    'observations_count' => count($createdObservations),
                    'observations' => $createdObservations,
                ],
            ]);
        });
    }
}
