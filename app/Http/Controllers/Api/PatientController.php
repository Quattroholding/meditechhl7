<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePatientRequest;
use App\Http\Resources\Api\PatientMedicalHistoryResource;
use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\File;
use App\Models\Patient;
use App\Models\User;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request): JsonResponse
    {
        $query = Patient::with(['primaryInsurance', 'secondaryInsurance']);

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('whatsapp_phone', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('active', true)->whereNull('deceased_date');
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            } elseif ($request->status === 'deceased') {
                $query->whereNotNull('deceased_date');
            }
        }

        // Filter by gender
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by age range
        if ($request->has('age_from') || $request->has('age_to')) {
            if ($request->has('age_from')) {
                $ageFrom = (int) $request->age_from;
                $dateFrom = now()->subYears($ageFrom + 1)->format('Y-m-d');
                $query->where('birth_date', '<=', $dateFrom);
            }
            if ($request->has('age_to')) {
                $ageTo = (int) $request->age_to;
                $dateTo = now()->subYears($ageTo)->format('Y-m-d');
                $query->where('birth_date', '>=', $dateTo);
            }
        }

        // Ordering
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');

        $allowedOrderColumns = ['name', 'created_at', 'updated_at', 'birth_date', 'identifier'];
        if (in_array($orderBy, $allowedOrderColumns)) {
            $query->orderBy($orderBy, $orderDirection);
        }

        // Pagination
        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
        $patients = $query->paginate($perPage);

        return response()->json([
            'data' => $patients->map(function ($patient) {
                $profile_picture_path = '';
                if ($patient->avatar()) {
                    $profile_picture_path = url('storage/'.$patient->avatar()->path);
                }

                return [
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
                    'phone' => $patient->phone,
                    'whatsapp_phone' => $patient->whatsapp_phone,
                    'email' => $patient->email,
                    'marital_status' => $patient->marital_status,
                    'blood_type' => $patient->blood_type,
                    'active' => $patient->active,
                    'deceased' => $patient->deceased,
                    'deceased_date' => $patient->deceased_date,
                    'primary_insurance' => $patient->primaryInsurance ? [
                        'id' => $patient->primaryInsurance->id,
                        'name' => $patient->primaryInsurance->name,
                        'policy_number' => $patient->primaryInsurance->policy_number,
                    ] : null,
                    'secondary_insurance' => $patient->secondaryInsurance ? [
                        'id' => $patient->secondaryInsurance->id,
                        'name' => $patient->secondaryInsurance->name,
                        'policy_number' => $patient->secondaryInsurance->policy_number,
                    ] : null,
                    'profile_picture_url' => $profile_picture_path,
                    'created_at' => $patient->created_at,
                    'updated_at' => $patient->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $patients->currentPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
                'last_page' => $patients->lastPage(),
                'from' => $patients->firstItem(),
                'to' => $patients->lastItem(),
                'has_more_pages' => $patients->hasMorePages(),
            ],
            'links' => [
                'first' => $patients->url(1),
                'last' => $patients->url($patients->lastPage()),
                'prev' => $patients->previousPageUrl(),
                'next' => $patients->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a new patient (for API v1)
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        try {

            // Generate FHIR ID
            $fhirId = 'patient-'.Str::uuid();
            $password = Str::password(8);

            // Parse name intelligently
            $parsedName = $this->parseFullName($request->name);
            $firstName = $parsedName['first_name'];
            $lastName = $parsedName['last_name'];

            // Generate a valid email if not provided
            if ($request->has('email') && ! empty($request->get('email'))) {
                $email = strtolower($request->email);
            } else {
                // Normalize names by removing accents and special characters
                $normalizedFirst = $this->normalizeForEmail($firstName);
                $normalizedLast = $this->normalizeForEmail($lastName);

                // Create base email from normalized names
                $baseEmail = strtolower($normalizedFirst.'.'.$normalizedLast);

                // Ensure uniqueness by adding a random suffix if email exists
                $email = $baseEmail.'@example.com';
                $counter = 1;
                while (User::whereEmail($email)->exists()) {
                    $email = $baseEmail.$counter.'@example.com';
                    $counter++;
                }
            }

            if (User::whereEmail($email)->exists()) {
                $model = User::whereEmail($email)->first();
            } else {
                $model = new User;
                $model->first_name = $firstName ?? $request->name;
                $model->last_name = $lastName ?? 'Apellido';
                $model->email = $email;
                $model->password = $password;
                $model->whatsapp_phone = $request->phone;
                $model->save();
            }

            // Asignar rol de paciente
            $model->assignRole('paciente');

            $identification_type = 'CC';
            if ($request->has('identification_type')) {
                $identification_type = $request->get('identification_type');
            }

            $gender = 'unknown';
            if($request->has('gender') && !empty($request->gender)){
                $gender = 'male';
                if($request->gender == 'M'){ $gender = 'male'; }
                if($request->gender == 'F'){ $gender = 'female'; }
            }

            // Create patient record
            $patient = Patient::create([
                'fhir_id' => $fhirId,
                'name' => $firstName.' '.$lastName,
                'given_name' => $firstName,
                'family_name' => $lastName,
                'email' => $email,
                'user_id' => $model->id,
                'identifier' => $request->identifier,
                'identifier_type' => $identification_type,
                'phone' => $request->phone,
                'whatsapp_phone' => $request->phone,
                'birth_date'=>$request->birth_date,
                'gender'=>$gender,
                'active' => true,
                'creation_source'=>'whatsapp'
            ]);

            if ($patient->save()) {
                // Handle ID document upload if provided
                $documentUploaded = false;

                // CASO 1: Archivo subido directamente
                if ($request->hasFile('id_document') && $request->file('id_document')->isValid()) {
                    try {
                        $fileService = new FileService;
                        $data = [
                            'folder' => 'patients',
                            'record_id' => $patient->id,
                            'type' => 'document',
                        ];
                        $file = $request->file('id_document');
                        $files = $fileService->guardarArchivos([$file], $data);
                        $documentUploaded = count($files) > 0;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error uploading patient ID document', [
                            'patient_id' => $patient->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                // CASO 2: Base64 string (desde WhatsApp/OCR)
                elseif ($request->has('id_document') && is_string($request->id_document)) {
                    try {
                        $base64String = $request->id_document;

                        // Validar que no sea [object Object] o similar
                        if (strpos($base64String, '[object') !== false || strpos($base64String, 'Object]') !== false) {
                            \Illuminate\Support\Facades\Log::error('Error: Se recibió [object Object] en lugar de base64', [
                                'patient_id' => $patient->id,
                                'received_value' => $base64String,
                            ]);
                            throw new \Exception('El valor de id_document no es válido. Se recibió un objeto en lugar de una cadena base64. Por favor, envíe la imagen como base64 string.');
                        }

                        // Limpiar el string base64 (remover espacios, saltos de línea, etc.)
                        $base64String = trim($base64String);
                        $base64String = str_replace([' ', "\n", "\r", "\t"], '', $base64String);

                        // Detectar tipo de imagen y remover prefijo si existe
                        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                            $imageType = strtolower($matches[1]);
                            $base64Data = substr($base64String, strpos($base64String, ',') + 1);
                        } elseif (preg_match('/^data:application\/(\w+);base64,/', $base64String, $matches)) {
                            // Soporte para PDFs
                            $imageType = strtolower($matches[1]);
                            $base64Data = substr($base64String, strpos($base64String, ',') + 1);
                        } else {
                            // Si no tiene prefijo, intentar detectar por contenido
                            $base64Data = $base64String;

                            // Decodificar para detectar el tipo
                            $testData = base64_decode($base64Data, true);
                            if ($testData !== false) {
                                // Detectar tipo por magic bytes
                                if (substr($testData, 0, 4) === "\xFF\xD8\xFF\xE0" || substr($testData, 0, 4) === "\xFF\xD8\xFF\xE1") {
                                    $imageType = 'jpg';
                                } elseif (substr($testData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                                    $imageType = 'png';
                                } elseif (substr($testData, 0, 4) === '%PDF') {
                                    $imageType = 'pdf';
                                } else {
                                    $imageType = 'jpg'; // Default
                                }
                            } else {
                                $imageType = 'jpg';
                            }
                        }

                        // Limpiar nuevamente el base64Data
                        $base64Data = str_replace([' ', "\n", "\r", "\t"], '', $base64Data);

                        // Decodificar base64 con validación estricta
                        $imageData = base64_decode($base64Data, true);

                        if ($imageData !== false && strlen($imageData) > 0) {
                            // Crear carpeta si no existe
                            $folderPath = storage_path('app/public/patients');
                            if (! file_exists($folderPath)) {
                                mkdir($folderPath, 0755, true);
                            }

                            // Generar nombre único
                            $fileName = 'patient_'.$patient->id.'_document_'.time().'.'.$imageType;
                            $filePath = $folderPath.'/'.$fileName;

                            // Guardar archivo
                            file_put_contents($filePath, $imageData);

                            // Verificar que el archivo se guardó correctamente
                            $fileSize = filesize($filePath);

                            // Registrar en la tabla de archivos manualmente
                            $user_id = $patient->user_id ?? null;

                            File::create([
                                'user_id' => $user_id,
                                'table_name' => 'patients',
                                'record_id' => $patient->id,
                                'name' => $fileName,
                                'path' => 'patients/'.$fileName,
                                'extention' => $imageType,
                                'type' => 'document',
                            ]);

                            $documentUploaded = true;

                            \Log::info('Documento base64 procesado exitosamente', [
                                'patient_id' => $patient->id,
                                'file_name' => $fileName,
                                'file_size' => $fileSize,
                                'image_type' => $imageType,
                                'base64_length' => strlen($base64Data),
                                'decoded_length' => strlen($imageData),
                                'first_bytes' => bin2hex(substr($imageData, 0, 4)),
                            ]);
                        } else {
                            \Log::error('Error: base64_decode falló o datos vacíos', [
                                'patient_id' => $patient->id,
                                'base64_length' => strlen($base64Data),
                                'first_chars' => substr($base64Data, 0, 20),
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error procesando documento base64', [
                            'patient_id' => $patient->id,
                            'error' => $e->getMessage(),
                        ]);
                        // Continuar sin documento si falla
                    }
                }

                $client = Client::first();
                $registrationData = [
                    'username' => $email,
                    'password' => $password,
                ];
                if ($request->has('email')) {
                    Mail::to($request->get('email'))->bcc('business@meditecpty.com')->send(new PatientWelcomeMail($patient, $client, $registrationData));
                } else {
                    Mail::to('business@meditecpty.com')->send(new PatientWelcomeMail($patient, $client, $registrationData));
                }

            }

            return response()->json([
                'message' => 'Paciente creado exitosamente.',
                'data' => [
                    'id' => $patient->id,
                    'fhir_id' => $patient->fhir_id,
                    'name' => $patient->name,
                    'identifier' => $patient->identifier,
                    'phone' => $patient->phone,
                    'whatsapp_phone' => $patient->whatsapp_phone,
                    'active' => $patient->active,
                    'id_document_uploaded' => $documentUploaded ?? false,
                    'created_at' => $patient->created_at,
                    'updated_at' => $patient->updated_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el paciente.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a patient's medical history by patient ID (for API v1)
     */
    public function getMedicalHistory(Request $request, int $patientId): JsonResponse
    {
        // Find the patient
        $patient = Patient::with([
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
        ])->find($patientId);

        if (! $patient) {
            return response()->json([
                'message' => 'Paciente no encontrado.',
            ], 404);
        }

        // Get profile picture
        $profile_picture_path = '';
        if ($patient->avatar()) {
            $profile_picture_path = url('storage/'.$patient->avatar()->path);
        }

        return response()->json([
            'data' => [
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
                    'profile_picture_url' => $profile_picture_path,
                ],
                'medical_history' => $patient->medicalHistories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'fhir_id' => $history->fhir_id,
                        'category' => $history->category,
                        'title' => $history->title,
                        'description' => $history->description,
                        'recorded_date' => $history->recorded_date,
                        'occurrence_date' => $history->occurrence_date,
                        'clinical_status' => $history->clinical_status,
                        'verification_status' => $history->verification_status,
                        'created_at' => $history->created_at,
                        'updated_at' => $history->updated_at,
                    ];
                }),
                'encounters' => $patient->encounters->map(function ($encounter) {
                    return [
                        'id' => $encounter->id,
                        'fhir_id' => $encounter->fhir_id,
                        'identifier' => $encounter->identifier,
                        'status' => $encounter->status,
                        'class' => $encounter->class,
                        'type' => $encounter->type,
                        'priority' => $encounter->priority,
                        'reason' => $encounter->reason,
                        'start' => $encounter->start,
                        'end' => $encounter->end,
                        'practitioner' => $encounter->practitioner ? [
                            'id' => $encounter->practitioner->id,
                            'name' => $encounter->practitioner->name,
                        ] : null,
                        'appointment_id' => $encounter->appointment_id,
                        'medical_speciality_id' => $encounter->medical_speciality_id,
                        'created_at' => $encounter->created_at,
                        'updated_at' => $encounter->updated_at,
                    ];
                }),
                'conditions' => $patient->conditions->map(function ($condition) {
                    return [
                        'id' => $condition->id,
                        'fhir_id' => $condition->fhir_id,
                        'identifier' => $condition->identifier,
                        'clinical_status' => $condition->clinical_status,
                        'verification_status' => $condition->verification_status,
                        'code' => $condition->code,
                        'category' => $condition->category,
                        'severity' => $condition->severity,
                        'onset_date' => $condition->onset_date,
                        'onset_info' => $condition->onset_info,
                        'abatement_date' => $condition->abatement_date,
                        'recorded_date' => $condition->recorded_date,
                        'note' => $condition->note,
                        'evidence' => $condition->evidence,
                        'icd10_code' => $condition->icd10Code ? [
                            'code' => $condition->icd10Code->code,
                            'description' => $condition->icd10Code->description_es,
                        ] : null,
                        'practitioner' => $condition->practitioner ? [
                            'id' => $condition->practitioner->id,
                            'name' => $condition->practitioner->name,
                        ] : null,
                        'created_at' => $condition->created_at,
                        'updated_at' => $condition->updated_at,
                    ];
                }),
                'vital_signs' => $patient->vitalSigns->map(function ($vitalSign) {
                    return [
                        'id' => $vitalSign->id,
                        'fhir_id' => $vitalSign->fhir_id,
                        'status' => $vitalSign->status,
                        'code' => $vitalSign->code,
                        'category' => $vitalSign->category,
                        'description' => $vitalSign->observationType->name,
                        'value' => $vitalSign->value,
                        'unit' => $vitalSign->observationType->default_unit,
                        'min_normal_value' => $vitalSign->observationType->min_normal_value,
                        'max_normal_value' => $vitalSign->observationType->max_normal_value,
                        'interpretation' => $vitalSign->interpretation,
                        'note' => $vitalSign->note,
                        'effective_date' => $vitalSign->effective_date,
                        'issued_date' => $vitalSign->issued_date,
                        'body_site' => $vitalSign->body_site,
                        'method' => $vitalSign->method,
                        'practitioner' => $vitalSign->practitioner ? [
                            'id' => $vitalSign->practitioner->id,
                            'name' => $vitalSign->practitioner->name,
                        ] : null,
                        'encounter_id' => $vitalSign->encounter_id,
                        'created_at' => $vitalSign->created_at,
                        'updated_at' => $vitalSign->updated_at,
                    ];
                }),
                'physical_exams' => $patient->physicalExams->map(function ($exam) {
                    return [
                        'id' => $exam->id,
                        'fhir_id' => $exam->fhir_id,
                        'status' => $exam->status,
                        'category' => $exam->category,
                        'code' => $exam->code,
                        'description' => $exam->observationType->name,
                        'method' => $exam->method,
                        'conclusion' => $exam->conclusion,
                        'effective_date' => $exam->effective_date,
                        'body_site' => $exam->body_site,
                        'finding' => $exam->finding,
                        'media' => $exam->media,
                        'practitioner' => $exam->practitioner ? [
                            'id' => $exam->practitioner->id,
                            'name' => $exam->practitioner->name,
                        ] : null,
                        'encounter_id' => $exam->encounter_id,
                        'created_at' => $exam->created_at,
                        'updated_at' => $exam->updated_at,
                    ];
                }),
                'medications' => $patient->medicationRequests->map(function ($medication) {
                    return [
                        'id' => $medication->id,
                        'fhir_id' => $medication->fhir_id,
                        'identifier' => $medication->identifier,
                        'status' => $medication->status,
                        'intent' => $medication->intent,
                        'priority' => $medication->priority,
                        'reason' => $medication->reason,
                        'dosage_instruction' => $medication->dosage_instruction,
                        'dosage_text' => $medication->dosage_text,
                        'route' => $medication->route,
                        'frequency' => $medication->frequency,
                        'quantity' => $medication->quantity,
                        'refills' => $medication->refills,
                        'valid_from' => $medication->valid_from,
                        'valid_to' => $medication->valid_to,
                        'substitution_allowed' => $medication->substitution_allowed,
                        'note' => $medication->note,
                        'medication' => $medication->medication,
                        'medicine' => $medication->medicine ? [
                            'id' => $medication->medicine->id,
                            'name' => $medication->medicine->full_name,
                        ] : null,
                        'practitioner' => $medication->practitioner ? [
                            'id' => $medication->practitioner->id,
                            'name' => $medication->practitioner->name,
                        ] : null,
                        'encounter_id' => $medication->encounter_id,
                        'created_at' => $medication->created_at,
                        'updated_at' => $medication->updated_at,
                    ];
                }),
                'service_requests' => $patient->serviceRequests->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'fhir_id' => $service->fhir_id,
                        'identifier' => $service->identifier,
                        'status' => $service->status,
                        'intent' => $service->intent,
                        'priority' => $service->priority,
                        'code' => $service->code,
                        'description' => $service->cpt->description_es,
                        'category' => $service->category,
                        'quantity' => $service->quantity,
                        'subject' => $service->subject,
                        'occurrence_date' => $service->occurrence_date,
                        'authored_on' => $service->authored_on,
                        'requester_reference' => $service->requester_reference,
                        'performer_type' => $service->performer_type,
                        'reason_code' => $service->reason_code,
                        'note' => $service->note,
                        'practitioner' => $service->practitioner ? [
                            'id' => $service->practitioner->id,
                            'name' => $service->practitioner->name,
                        ] : null,
                        'encounter_id' => $service->encounter_id,
                        'created_at' => $service->created_at,
                        'updated_at' => $service->updated_at,
                    ];
                }),
                'procedures' => $patient->procedures->map(function ($procedure) {
                    return [
                        'id' => $procedure->id,
                        'fhir_id' => $procedure->fhir_id,
                        'identifier' => $procedure->identifier,
                        'status' => $procedure->status,
                        'category' => $procedure->category,
                        'code' => $procedure->code,
                        'performed_date' => $procedure->performed_date,
                        'location' => $procedure->location,
                        'outcome' => $procedure->outcome,
                        'note' => $procedure->note,
                        'practitioner' => $procedure->practitioner ? [
                            'id' => $procedure->practitioner->id,
                            'name' => $procedure->practitioner->name,
                        ] : null,
                        'encounter_id' => $procedure->encounter_id,
                        'created_at' => $procedure->created_at,
                        'updated_at' => $procedure->updated_at,
                    ];
                }),
                'allergies' => $patient->allergies->map(function ($allergy) {
                    return [
                        'id' => $allergy->id,
                        'fhir_id' => $allergy->fhir_id,
                        'category' => $allergy->category,
                        'title' => $allergy->title,
                        'description' => $allergy->description,
                        'recorded_date' => $allergy->recorded_date,
                        'occurrence_date' => $allergy->occurrence_date,
                        'clinical_status' => $allergy->clinical_status,
                        'verification_status' => $allergy->verification_status,
                        'created_at' => $allergy->created_at,
                        'updated_at' => $allergy->updated_at,
                    ];
                }),
            ],
        ]);
    }

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
        $profile_picture_path = '';
        if ($patient->avatar()) {
            $profile_picture_path = url('storage/'.$patient->avatar()->path);
        }

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
                    'profile_picture_url' => $profile_picture_path,
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
        $patient = DB::table('patients')->where('user_id', $user->id)->first();
        if (! $patient) {
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
                'max:5120', // 5MB max
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
            $fileService = new FileService;
            $file = $request->file('profile_picture');
            $ext = $file->getClientOriginalExtension();

            $filename = 'patient_profile_'.$user->id.'_'.time();

            // Subir el archivo
            $profilePicturePath = $fileService->uploadSingleFile(
                $request->file('profile_picture'),
                'patients',
                $filename
            );

            if ($profilePicturePath) {
                // Actualizar el campo profile_picture en la tabla users usando DB directo
                DB::table('users')->where('id', $user->id)->update([
                    'profile_picture' => $profilePicturePath,
                    'updated_at' => now(),
                ]);

                DB::table('files')->insert([
                    'user_id' => $user->id,
                    'table_name' => 'patients',
                    'record_id' => $patient->id,
                    'name' => basename($profilePicturePath),
                    'path' => $profilePicturePath,
                    'extention' => $ext,
                    'type' => 'avatar',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Get updated user data
                $updatedUser = DB::table('users')->where('id', $user->id)->first();

                return response()->json([
                    'message' => 'Foto de perfil actualizada exitosamente.',
                    'data' => [
                        'profile_picture' => $profilePicturePath,
                        'profile_picture_url' => url('storage/'.$profilePicturePath),
                        'updated_at' => $updatedUser->updated_at,
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
            $profile_picture_path = '';
            if ($patient->avatar()) {
                $profile_picture_path = url('storage/'.$patient->avatar()->path);
            }
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
                    'profile_picture_url' => $profile_picture_path,
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

    /**
     * Parse full name intelligently into first name and last name
     *
     * Handles 3 cases:
     * - 2 words: "Rafael Gasperi" → first: "Rafael", last: "Gasperi"
     * - 3 words: "Rafael Gasperi Martinez" → first: "Rafael", last: "Gasperi Martinez"
     * - 4+ words: "Rafael Roberto Gasperi Martinez" → first: "Rafael Roberto", last: "Gasperi Martinez"
     */
    private function parseFullName(string $fullName): array
    {
        // Limpiar espacios extras
        $fullName = trim(preg_replace('/\s+/', ' ', $fullName));

        // Dividir el nombre en partes
        $nameParts = explode(' ', $fullName);
        $partCount = count($nameParts);

        $firstName = '';
        $lastName = '';

        switch ($partCount) {
            case 0:
                // Nombre vacío
                $firstName = '';
                $lastName = '';
                break;

            case 1:
                // Solo un nombre: "Rafael"
                $firstName = ucwords(strtolower($nameParts[0]));
                $lastName = '';
                break;

            case 2:
                // Dos palabras: "Rafael Gasperi"
                // Nombre: Rafael, Apellido: Gasperi
                $firstName = ucwords(strtolower($nameParts[0]));
                $lastName = ucwords(strtolower($nameParts[1]));
                break;

            case 3:
                // Tres palabras: "Rafael Gasperi Martinez"
                // Nombre: Rafael, Apellidos: Gasperi Martinez
                $firstName = ucwords(strtolower($nameParts[0]));
                $lastName = ucwords(strtolower($nameParts[1].' '.$nameParts[2]));
                break;

            default:
                // 4 o más palabras: "Rafael Roberto Gasperi Martinez"
                // Primeras mitad: Nombres, Segunda mitad: Apellidos
                $middlePoint = (int) ceil($partCount / 2);

                $firstNameParts = array_slice($nameParts, 0, $middlePoint);
                $lastNameParts = array_slice($nameParts, $middlePoint);

                $firstName = ucwords(strtolower(implode(' ', $firstNameParts)));
                $lastName = ucwords(strtolower(implode(' ', $lastNameParts)));
                break;
        }

        return [
            'first_name' => $firstName ?: 'Sin Nombre',
            'last_name' => $lastName ?: 'Sin Apellido',
            'full_name' => $fullName,
        ];
    }

    // ENDPOINT DE BÚSQUEDA DE PACIENTES
    public function search(Request $request)
    {
        $search = $request->get('search');

        $patients = Patient::where(function ($query) use ($search) {
            // Buscar en nombre
            $query->where('given_name', 'LIKE', "%{$search}%")
                  // Buscar en apellido
                ->orWhere('family_name', 'LIKE', "%{$search}%")
                  // Buscar en nombre completo concatenado
                ->orWhereRaw("CONCAT(given_name, ' ', family_name) LIKE ?", ["%{$search}%"])
                  // Si tienes campo de identificación
                ->orWhere('identifier', 'LIKE', "%{$search}%");
        })
            ->select('id', 'given_name', 'family_name', 'identifier')
            ->limit(10)
            ->get();

        $results = $patients->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->given_name.' '.$patient->family_name,
                'identifier' => $patient->identifier,
            ];
        });

        return response()->json([
            'results' => $results,
            'total' => Patient::where('given_name', 'LIKE', "%{$search}%")
                ->orWhere('family_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(given_name, ' ', family_name) LIKE ?", ["%{$search}%"])
                ->count(),
        ]);
    }

    /**
     * Normalize a string for use in email addresses
     * Removes accents, special characters, and spaces
     */
    private function normalizeForEmail(string $text): string
    {
        // Remove accents and convert to ASCII
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        // Remove any non-alphanumeric characters except dots and hyphens
        $text = preg_replace('/[^a-zA-Z0-9\.\-]/', '', $text);

        // Remove multiple consecutive dots or hyphens
        $text = preg_replace('/[.\-]+/', '.', $text);

        // Remove leading/trailing dots or hyphens
        $text = trim($text, '.-');

        return $text ?: 'user';
    }
}
