<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PatientMedicalHistoryResource;
use App\Models\Patient;
use App\Models\User;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PatientController extends Controller
{
    /**
     * Get the authenticated patient's profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Verify that the user has a patient profile
        if (! $user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        $patient = $user->patient()->with(['clients', 'primaryInsurance', 'secondaryInsurance'])->first();

        return response()->json([
            'data' => [
                'patient' => [
                    'id' => $patient->id,
                    'fhir_id' => $patient->fhir_id,
                    'identifier' => $patient->identifier,
                    'identifier_type' => $patient->identifier_type,
                    'name' => $patient->name,
                    'given_name' => $patient->given_name,
                    'family_name' => $patient->family_name,
                    'gender' => $patient->gender,
                    'birth_date' => $patient->birth_date,
                    'age' => $patient->age,
                    'address' => $patient->address,
                    'city' => $patient->city,
                    'state' => $patient->state,
                    'postal_code' => $patient->postal_code,
                    'country' => $patient->country,
                    'phone' => $patient->phone,
                    'whatsapp_phone' => $patient->whatsapp_phone,
                    'email' => $patient->email,
                    'marital_status' => $patient->marital_status,
                    'blood_type' => $patient->blood_type,
                    'multiple_birth' => $patient->multiple_birth,
                    'multiple_birth_count' => $patient->multiple_birth_count,
                    'deceased' => $patient->deceased,
                    'deceased_date' => $patient->deceased_date,
                    'primary_insurance' => $patient->primaryInsurance,
                    'secondary_insurance' => $patient->secondaryInsurance,
                    'created_at' => $patient->created_at,
                    'updated_at' => $patient->updated_at,
                ],
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'profile_picture' => $user->profile_picture,
                    'whatsapp_phone' => $user->whatsapp_phone,
                    'active' => $user->active,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ],
        ]);
    }

    /**
     * Update patient personal data
     */
    public function updatePersonalData(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        $regex = $this->getIdPattern($request->identifier_type);

        // Manual validation
        $validator = Validator::make($request->all(), [
            'given_name' => ['required', 'string', 'max:255'],
            'family_name' => ['required', 'string', 'max:255'],
            'identifier_type' => ['required', 'string', 'in:CC,CE,PA,SS,PT'],
            'identifier' => ['required', 'regex:'.$regex],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'birth_date' => ['required', 'date', 'before:today'],
            'address' => ['sometimes', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'marital_status' => ['sometimes', 'string', 'in:soltero,casado,divorciado,viudo,separado,unido'],
            'blood_type' => ['sometimes', 'nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        ], [
            'identifier_type.string' => 'El tipo de identificacion debe ser el correcto.',
            'identifier.regex' => 'El numero de identificacion debe ser el correcto.',
            'given_name.string' => 'El nombre debe ser una cadena de texto.',
            'family_name.string' => 'El apellido debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'gender.in' => 'El género debe ser: masculino, femenino u otro.',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $patient = $user->patient;
        $data = $validator->validated();
        $data['name'] = $request->given_name.' '.$data['family_name'];
        $data['whatsapp_phone'] = $request->phone;

        // Format birth_date if present to avoid MySQL date format error
        if (isset($data['birth_date'])) {
            try {
                $data['birth_date'] = Carbon::parse($data['birth_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['birth_date' => ['Formato de fecha inválido']],
                ], 422);
            }
        }

        // Use direct DB query to avoid PatientScope issues with Sanctum
        DB::table('patients')->where('id', $patient->id)->update($data);

        // Refresh patient data
        $patient->refresh();

        return response()->json([
            'message' => 'Datos actualizados exitosamente.',
            'data' => [
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'given_name' => $patient->given_name,
                    'family_name' => $patient->family_name,
                    'gender' => $patient->gender,
                    'birth_date' => $patient->birth_date,
                    'address' => $patient->address,
                    'city' => $patient->city,
                    'state' => $patient->state,
                    'postal_code' => $patient->postal_code,
                    'country' => $patient->country,
                    'phone' => $patient->phone,
                    'whatsapp_phone' => $patient->whatsapp_phone,
                    'email' => $patient->email,
                    'marital_status' => $patient->marital_status,
                    'blood_type' => $patient->blood_type,
                    'updated_at' => $patient->updated_at,
                ],
            ],
        ]);
    }

    /**
     * Update patient access credentials (user data)
     */
    public function updateCredentials(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        // Manual validation
        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($user->patient->id)],
            'password' => ['sometimes', 'nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'whatsapp_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Only update password if provided
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        // Use direct DB query to avoid any scope issues
        DB::table('users')->where('id', $user->id)->update($data);

        // Refresh user data
        $user->refresh();

        return response()->json([
            'message' => 'Datos de acceso actualizados exitosamente.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'whatsapp_phone' => $user->whatsapp_phone,
                    'updated_at' => $user->updated_at,
                ],
            ],
        ]);
    }

    /**
     * Update patient profile picture
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        // Validar el archivo de imagen
        $validator = Validator::make($request->all(), [
            'profile_picture' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120' // 5MB max
            ],
        ], [
            'profile_picture.required' => 'La imagen de perfil es obligatoria.',
            'profile_picture.file' => 'Debe ser un archivo válido.',
            'profile_picture.image' => 'El archivo debe ser una imagen.',
            'profile_picture.mimes' => 'La imagen debe ser formato: jpeg, jpg, png, gif o webp.',
            'profile_picture.max' => 'La imagen no puede ser mayor a 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $fileService = new FileService();
            $filename = 'patient_profile_' . $user->id . '_' . time();
            
            // Subir el archivo
            $profilePicturePath = $fileService->uploadSingleFile(
                $request->file('profile_picture'),
                'patients',
                $filename
            );

            if ($profilePicturePath) {
                // Actualizar el campo profile_picture en la tabla users
                $user->profile_picture = $profilePicturePath;
                $user->save();

                return response()->json([
                    'message' => 'Foto de perfil actualizada exitosamente.',
                    'data' => [
                        'profile_picture' => $profilePicturePath,
                        'profile_picture_url' => url('storage/' . $profilePicturePath),
                        'updated_at' => $user->updated_at,
                    ],
                ]);
            }

            return response()->json([
                'message' => 'Error al subir la imagen.',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated patient's complete medical history
     */
    public function medicalHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        $perPage = $request->input('per_page');
        $usePagination = $perPage !== null;

        if ($usePagination) {
            $perPage = min(max((int) $perPage, 1), 100); // Limit between 1 and 100

            // Get patient basic info without eager loading collections
            $patient = $user->patient;

            // Build response with paginated collections
            $response = [
                'patient_info' => [
                    'id' => $patient->id,
                    'fhir_id' => $patient->fhir_id,
                    'identifier' => $patient->identifier,
                    'identifier_type' => $patient->identifier_type,
                    'name' => $patient->name,
                    'given_name' => $patient->given_name,
                    'family_name' => $patient->family_name,
                    'gender' => $patient->gender,
                    'birth_date' => $patient->birth_date,
                    'age' => $patient->age,
                    'blood_type' => $patient->blood_type,
                    'marital_status' => $patient->marital_status,
                    'phone' => $patient->phone,
                    'email' => $patient->email,
                ],
                'collections' => [],
            ];

            // Get paginated collections based on request
            $collections = [
                'medical_history' => $patient->medicalHistories(),
                'encounters' => $patient->encounters()->with(['practitioner', 'serviceRequests', 'vitalSigns']),
                'conditions' => $patient->conditions()->with(['icd10Code', 'practitioner']),
                'vital_signs' => $patient->vitalSigns()->with('practitioner'),
                'physical_exams' => $patient->physicalExams()->with('practitioner'),
                'medications' => $patient->medicationRequests()->with(['medicine', 'practitioner']),
                'services' => $patient->serviceRequests()->with('practitioner'),
                'procedures' => $patient->procedures()->with('practitioner'),
                'allergies' => $patient->allergies(),
            ];

            foreach ($collections as $key => $query) {
                $paginated = $query->paginate($perPage, ['*'], $key.'_page');
                $response['collections'][$key] = [
                    'data' => $this->formatCollectionData($key, $paginated->items()),
                    'pagination' => [
                        'current_page' => $paginated->currentPage(),
                        'per_page' => $paginated->perPage(),
                        'total' => $paginated->total(),
                        'last_page' => $paginated->lastPage(),
                        'from' => $paginated->firstItem(),
                        'to' => $paginated->lastItem(),
                        'has_more_pages' => $paginated->hasMorePages(),
                    ],
                ];
            }

            return response()->json(['data' => $response]);
        } else {
            // Original behavior - return all data without pagination
            $patient = $user->patient()
                ->with([
                    'medicalHistories',
                    'encounters.practitioner',
                    'encounters.serviceRequests',
                    'encounters.vitalSigns',
                    'conditions.icd10Code',
                    'conditions.practitioner',
                    'vitalSigns.practitioner',
                    'physicalExams.practitioner',
                    'medicationRequests.medicine',
                    'medicationRequests.practitioner',
                    'serviceRequests.practitioner',
                    'procedures.practitioner',
                    'allergies',
                ])
                ->first();

            return response()->json([
                'data' => new PatientMedicalHistoryResource($patient),
            ]);
        }
    }

    /**
     * Get a specific section of the patient's medical history
     */
    public function medicalHistorySection(Request $request, string $section): JsonResponse
    {
        $user = $request->user();

        if (! $user->patient) {
            return response()->json([
                'message' => 'Perfil del paciente no encontrado.',
            ], 404);
        }

        $validSections = [
            'info', 'medical-history', 'encounters', 'conditions',
            'vital-signs', 'physical-exams', 'medications',
            'services', 'procedures', 'allergies',
        ];

        if (! in_array($section, $validSections)) {
            return response()->json([
                'message' => 'Sección no válida.',
                'available_sections' => $validSections,
            ], 400);
        }

        $patient = $user->patient;
        $data = $patient->getSectionHistory($section);

        // Format the response based on section
        $formattedData = match ($section) {
            'info' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'gender' => $patient->gender,
                'birth_date' => $patient->birth_date,
                'age' => $patient->age,
                'blood_type' => $patient->blood_type,
                'marital_status' => $patient->marital_status,
                'phone' => $patient->phone,
                'email' => $patient->email,
            ],
            'medical-history' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category' => $item->category,
                    'title' => $item->title,
                    'description' => $item->description,
                    'recorded_date' => $item->recorded_date,
                    'occurrence_date' => $item->occurrence_date,
                ];
            }),
            'encounters' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'reason' => $item->reason,
                    'start' => $item->start,
                    'end' => $item->end,
                    'practitioner' => $item->practitioner?->name,
                ];
            }),
            'conditions' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'clinical_status' => $item->clinical_status,
                    'severity' => $item->severity,
                    'description' => $item->icd10Code->description_es,
                    'onset_date' => Carbon::parse($item->onset_date)->format('d-m-Y'),
                    'recorded_date' => Carbon::parse($item->recorded_date)->format('d-m-Y'),
                ];
            }),
            'vital-signs' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'value' => $item->value,
                    'unit' => $item->unit,
                    'effective_date' => $item->effective_date,
                    'observation_type' => $item->observationType?->name ?? $item->code,
                ];
            }),
            'physical-exams' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'description' => $item->description,
                    'conclusion' => $item->conclusion,
                    'effective_date' => $item->effective_date,
                    'observation_type' => $item->observationType?->name ?? $item->code,
                ];
            }),
            'medications' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'dosage_text' => $item->dosage_text,
                    'route' => $item->route,
                    'frequency' => $item->frequency,
                    'valid_from' => $item->valid_from,
                    'valid_to' => $item->valid_to,
                ];
            }),
            'services' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'code' => $item->code,
                    'category' => $item->category,
                    'occurrence_date' => $item->occurrence_date,
                ];
            }),
            'procedures' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'status' => $item->status,
                    'performed_date' => $item->performed_date,
                    'outcome' => $item->outcome,
                ];
            }),
            'allergies' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'recorded_date' => $item->recorded_date,
                    'clinical_status' => $item->clinical_status,
                ];
            }),
        };

        return response()->json([
            'section' => $section,
            'data' => $formattedData,
        ]);
    }

    private function getIdPattern($type)
    {
        switch ($type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456
                return '/^[0-9]+-[0-9]+$/';
            case 'CE': // Cédula Extranjera: Similar a CC
                return '/^[A-Z]+-[0-9]+-[0-9]+$/';
            case 'PA': // Pasaporte: N1234567
                return '/^[A-Z0-9-]{5,20}$/';
            case 'PT': // Permiso Temporal: Formato flexible
                return '/^[A-Z0-9-]{8,15}$/';
            case 'SS': // Seguro Social: XXX-XX-XXXX
                return '/^\d{3}-?\d{2}-?\d{4}$/';
            default:
                return '/^[A-Z0-9-]{5,20}$/'; // Universal para cualquier tipo
        }
    }

    /**
     * Format collection data based on type
     */
    private function formatCollectionData(string $type, $items): array
    {
        return match ($type) {
            'medical_history' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'category' => $item->category,
                    'title' => $item->title,
                    'description' => $item->description,
                    'recorded_date' => $item->recorded_date,
                    'occurrence_date' => $item->occurrence_date,
                    'clinical_status' => $item->clinical_status,
                    'verification_status' => $item->verification_status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'encounters' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'identifier' => $item->identifier,
                    'status' => $item->status,
                    'class' => $item->class,
                    'type' => $item->type,
                    'priority' => $item->priority,
                    'reason' => $item->reason,
                    'start' => $item->start,
                    'end' => $item->end,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'appointment_id' => $item->appointment_id,
                    'medical_speciality_id' => $item->medical_speciality_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'conditions' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'identifier' => $item->identifier,
                    'clinical_status' => $item->clinical_status,
                    'verification_status' => $item->verification_status,
                    'code' => $item->code,
                    'category' => $item->category,
                    'severity' => $item->severity,
                    'onset_date' => $item->onset_date,
                    'abatement_date' => $item->abatement_date,
                    'recorded_date' => $item->recorded_date,
                    'note' => $item->note,
                    'evidence' => $item->evidence,
                    'icd10_code' => $item->icd10Code ? [
                        'code' => $item->icd10Code->code,
                        'description' => $item->icd10Code->description_es,
                    ] : null,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'vital_signs' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'status' => $item->status,
                    'code' => $item->code,
                    'category' => $item->category,
                    'value' => $item->value,
                    'unit' => $item->unit,
                    'interpretation' => $item->interpretation,
                    'note' => $item->note,
                    'effective_date' => $item->effective_date,
                    'issued_date' => $item->issued_date,
                    'body_site' => $item->body_site,
                    'method' => $item->method,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'encounter_id' => $item->encounter_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'physical_exams' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'status' => $item->status,
                    'category' => $item->category,
                    'code' => $item->code,
                    'description' => $item->description,
                    'method' => $item->method,
                    'conclusion' => $item->conclusion,
                    'effective_date' => $item->effective_date,
                    'body_site' => $item->body_site,
                    'finding' => $item->finding,
                    'media' => $item->media,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'encounter_id' => $item->encounter_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'medications' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'identifier' => $item->identifier,
                    'status' => $item->status,
                    'intent' => $item->intent,
                    'priority' => $item->priority,
                    'reason' => $item->reason,
                    'dosage_instruction' => $item->dosage_instruction,
                    'dosage_text' => $item->dosage_text,
                    'route' => $item->route,
                    'frequency' => $item->frequency,
                    'quantity' => $item->quantity,
                    'refills' => $item->refills,
                    'valid_from' => $item->valid_from,
                    'valid_to' => $item->valid_to,
                    'substitution_allowed' => $item->substitution_allowed,
                    'note' => $item->note,
                    'medicine' => $item->medicine ? [
                        'id' => $item->medicine->id,
                        'name' => $item->medicine->full_name,
                    ] : null,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'encounter_id' => $item->encounter_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'services' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'identifier' => $item->identifier,
                    'status' => $item->status,
                    'intent' => $item->intent,
                    'priority' => $item->priority,
                    'code' => $item->code,
                    'category' => $item->category,
                    'quantity' => $item->quantity,
                    'subject' => $item->subject,
                    'occurrence_date' => $item->occurrence_date,
                    'authored_on' => $item->authored_on,
                    'requester_reference' => $item->requester_reference,
                    'performer_type' => $item->performer_type,
                    'reason_code' => $item->reason_code,
                    'note' => $item->note,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'encounter_id' => $item->encounter_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'procedures' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'identifier' => $item->identifier,
                    'status' => $item->status,
                    'category' => $item->category,
                    'code' => $item->code,
                    'performed_date' => $item->performed_date,
                    'location' => $item->location,
                    'outcome' => $item->outcome,
                    'note' => $item->note,
                    'practitioner' => $item->practitioner ? [
                        'id' => $item->practitioner->id,
                        'name' => $item->practitioner->name,
                    ] : null,
                    'encounter_id' => $item->encounter_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            'allergies' => collect($items)->map(function ($item) {
                return [
                    'id' => $item->id,
                    'fhir_id' => $item->fhir_id,
                    'category' => $item->category,
                    'title' => $item->title,
                    'description' => $item->description,
                    'recorded_date' => $item->recorded_date,
                    'occurrence_date' => $item->occurrence_date,
                    'clinical_status' => $item->clinical_status,
                    'verification_status' => $item->verification_status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->toArray(),

            default => collect($items)->toArray(),
        };
    }
}
