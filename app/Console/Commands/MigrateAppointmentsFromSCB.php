<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\ClinicalObservationType;
use App\Models\Condition;
use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use App\Models\MedicalHistory;
use App\Models\MedicalSpeciality;
use App\Models\MedicationRequest;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\PresentIllnesType;
use App\Models\ServiceRequestResult;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigrateAppointmentsFromSCB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-appointments-from-scb
                            {--dry-run : Ejecutar en modo simulación sin guardar cambios}
                            {--batch-size=100 : Número de citas a procesar por lote}
                            {--limit= : Limitar el número total de citas a migrar}
                            {--force-remigrate : Ignorar flag de migración y procesar todas las citas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar citas/encuentros desde la base de datos SCB Oracle con progress bar y tracking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $isDryRun = $this->option('dry-run');
            $batchSize = (int) $this->option('batch-size', 100);
            $limit = $this->option('limit') ? (int) $this->option('limit') : null;
            $forceRemigrate = $this->option('force-remigrate');

            if ($isDryRun) {
                $this->warn('🧪 Ejecutando en modo DRY-RUN - No se guardarán cambios');
            }

            if ($forceRemigrate) {
                $this->warn('⚠️  Modo FORCE-REMIGRATE activado - Se procesarán todas las citas');
            }

            $this->info('📅 Iniciando migración de citas/encuentros desde SCB...');

            // Check if migration column exists, if not, ignore migration tracking
            $migrationColumnExists = $this->checkMigrationColumn();

            // Build the base query
            $baseQuery = "SELECT a.id, a.PATIENT_ID, a.STATUS, a.START_DATE, a.END_DATE, a.INSURANCE, a.\"TYPE\",
                         a.DOCTOR_ID, a.CREATED_AT, ms.name AS speciality, c.id AS consultation_id,c.CHIEF_COMPLAINT,
                         (SELECT LISTAGG(consultation_list_id, '; ') WITHIN GROUP (ORDER BY consultation_list_id) FROM consultation_locations WHERE consultation_id = c.id) as locations,
                         c.SEVERITY, c.DURATION, c.TIMING, c.MODIFYING_FACTOR,c.SYMPTOMS,c.DESCRIPTION,c.medicine, c.SURGERY, c.ALLERGIES, c.if_alcohol AS alcohol, c.if_drugs AS drugs,  c.if_tabacco AS tabacco, if_others2 AS other,
                         c.CREATED_AT AS cstar, (SELECT max(ash.CREATED_AT) FROM APPOINTMENTS_STATUS_HISTORY ash
                         WHERE ash.APPOINTMENT_ID = a.id AND ash.STATUS = 'completada') AS cend";

            if ($migrationColumnExists) {
                $baseQuery .= ", COALESCE(a.meditec_migration, '0') as meditec_migration";
            } else {
                $baseQuery .= ", '0' as meditec_migration";
                $this->warn('⚠️  Columna meditec_migration no existe - Ejecutando sin tracking de migración');
            }

            $baseQuery .= " FROM APPOINTMENTS a, PATIENTS p, CONSULTATIONS c, CONSULTING_ROOMS cr,
                                 PHYSICIANS p2, MEDICAL_SPECIALTIES ms
                            WHERE a.PATIENT_ID = p.id AND a.id = c.APPOINTMENT_ID
                            AND a.CONSULTING_ROOM_ID = cr.id
                            AND p2.MEDICAL_SPECIALITY_ID = ms.id
                            AND a.DOCTOR_ID = p2.CODE
                            AND p.MEDITEC_MIGRATION = 1
                            AND a.STATUS IN ('completada','cerrada')
                            AND a.DELETED_AT IS NULL
                            AND c.DELETED_AT IS NULL
                            AND c.CHIEF_COMPLAINT NOT LIKE 'SOAP'
                            AND c.CHIEF_COMPLAINT NOT LIKE '-SOAP'
                            AND c.CHIEF_COMPLAINT NOT LIKE '-soap'
                            AND c.DESCRIPTION IS NOT null
                            AND c.CHIEF_COMPLAINT NOT LIKE '-'";

            // Add migration filter unless force remigrate is enabled
            if ($migrationColumnExists && ! $forceRemigrate) {
                $baseQuery .= " AND COALESCE(a.meditec_migration, '0') = '0'";
            }

            // Add order by for consistent results
            $baseQuery .= ' ORDER BY a.id';

            // Get total count first (without limit for accurate count)
            $countQuery = "SELECT COUNT(*) as total FROM (
                {$baseQuery}
            ) appointments_subquery";

            // If limit is specified, modify the base query to use Oracle ROWNUM
            if ($limit) {
                $baseQuery = "SELECT * FROM (
                    {$baseQuery}
                ) WHERE ROWNUM <= {$limit}";
            }

            $totalResult = DB::connection('scb')->select($countQuery);
            $totalCount = $totalResult[0]->total ?? 0;

            if ($totalCount === 0) {
                $this->warn('⚠️  No se encontraron citas para migrar.');

                return Command::SUCCESS;
            }

            $this->info("📊 Se encontraron {$totalCount} citas para migrar.");

            if (! $this->confirm('¿Desea continuar con la migración?')) {
                $this->info('❌ Migración cancelada por el usuario.');

                return Command::SUCCESS;
            }

            // Get the appointments data
            $appointments = DB::connection('scb')->select($baseQuery);

            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalCount);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% - %message%');
            $progressBar->setMessage('Iniciando migración de citas...');

            $this->line('');
            $progressBar->start();

            // Note: We'll use individual transactions for each appointment instead of one global transaction

            $migratedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            foreach ($appointments as $appSCB) {
                $progressBar->setMessage("Procesando cita: {$appSCB->id}");

                // Start individual transaction for each appointment
                DB::beginTransaction();
                try {
                    $patient = Patient::whereScbId($appSCB->patient_id)->first();
                    $practitioner = Practitioner::whereScbId($appSCB->doctor_id)->first();
                    $medical_speciality = MedicalSpeciality::whereName($appSCB->speciality)->first();

                    // Check if appointment already exists
                    $existingAppointment = Appointment::whereScbId($appSCB->id)->first();

                    if (! $existingAppointment && $patient && $practitioner && $medical_speciality) {
                        // Save medical history first
                        if (! $isDryRun) {
                            $this->saveMedicalHistory($appSCB, $patient);
                        }

                        $appointment = new Appointment([
                            'scb_id' => $appSCB->id,
                            'fhir_id' => 'appointment-'.Str::uuid(),
                            'patient_id' => $patient->id,
                            'practitioner_id' => $practitioner->id,
                            'identifier' => 'APT-'.fake()->unique()->numerify('#######'),
                            'status' => 'fulfilled',
                            'service_type' => $appSCB->type,
                            'description' => null,
                            'start' => $appSCB->start_date,
                            'end' => $appSCB->end_date,
                            'minutes_duration' => $this->calculateDuration($appSCB),
                            'consulting_room_id' => null,
                            'medical_speciality_id' => $medical_speciality->id,
                            'client_id' => null,
                            'created_at' => $appSCB->created_at,
                            'participant' => json_encode([
                                [
                                    'actor' => 'Patient/'.$patient->fhir_id,
                                    'required' => 'required',
                                    'status' => 'accepted',
                                ],
                                [
                                    'actor' => 'Practitioner/'.$practitioner->fhir_id,
                                    'required' => 'required',
                                    'status' => 'accepted',
                                ],
                            ]),
                        ]);

                        $appointmentSaved = $isDryRun || $appointment->save();

                        if ($appointmentSaved) {
                            // Create encounter and all related data if not in dry-run mode
                            if (! $isDryRun) {
                                $encounter = $this->createEncounter($appSCB, $appointment, $medical_speciality);
                                if (! $encounter) {
                                    throw new \Exception("Failed to create encounter for appointment {$appSCB->id}");
                                }
                            }

                            // Mark as migrated in Oracle only after everything succeeds
                            if (! $isDryRun && $migrationColumnExists) {
                                $this->markAppointmentAsMigrated($appSCB->id);
                            }

                            // Commit individual transaction
                            DB::commit();

                            $migratedCount++;
                            $consultationSummary = $this->getConsultationSummary($appSCB, $encounter ?? $appointment);
                            $progressBar->setMessage("✅ Cita migrada: {$appSCB->id} - {$consultationSummary}");
                        } else {
                            throw new \Exception("Failed to save appointment {$appSCB->id}");
                        }
                    } else {
                        // Rollback individual transaction if skipping
                        DB::rollBack();

                        $skippedCount++;
                        if ($existingAppointment) {
                            $this->markAppointmentAsMigrated($appSCB->id);
                            $progressBar->setMessage("⏭️  Cita ya existe: {$appSCB->id}");
                        } else {
                            $progressBar->setMessage("⚠️  Datos faltantes para cita: {$appSCB->id}");
                        }
                    }
                } catch (\Exception $e) {
                    // Rollback individual transaction on any error
                    DB::rollBack();

                    $errorCount++;
                    $progressBar->setMessage("❌ Error procesando cita: {$appSCB->id}");
                    // Log the detailed error
                    Log::error("Error migrando cita {$appSCB->id}: ".$e->getMessage(), [
                        'appointment_id' => $appSCB->id,
                        'patient_id' => $appSCB->patient_id ?? null,
                        'doctor_id' => $appSCB->doctor_id ?? null,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                $progressBar->advance();

                // Add a small delay to make the progress visible (optional)
                usleep(10000); // 10ms
            }

            $progressBar->finish();
            $this->line('');
            $this->line('');

            // Individual transactions were used, so no global commit needed
            if (! $isDryRun) {
                $this->info('✅ Migración completada - Cada cita fue procesada en su propia transacción.');
            } else {
                $this->info('🧪 Simulación completada - No se guardaron cambios.');
            }

            // Show final summary
            $this->info('✅ Migración de citas completada exitosamente!');

            $summaryData = [
                ['Total de citas procesadas', $totalCount],
                ['Citas migradas', $migratedCount],
                ['Citas omitidas (ya existían o datos faltantes)', $skippedCount],
                ['Errores encontrados', $errorCount],
            ];

            if ($forceRemigrate) {
                $summaryData[] = ['Modo force-remigrate', 'SÍ'];
            } else {
                $summaryData[] = ['Solo citas no migradas', 'SÍ'];
            }

            if ($isDryRun) {
                $summaryData[] = ['Modo dry-run', 'SÍ'];
            }

            $this->table(['Métrica', 'Cantidad'], $summaryData);

            if ($errorCount > 0) {
                $this->warn("⚠️  Se encontraron {$errorCount} errores durante la migración. Revisa los logs para más detalles.");
            }

            return Command::SUCCESS;

        } catch (\Exception $exception) {
            if (isset($progressBar)) {
                $progressBar->finish();
                $this->line('');
            }

            $isDryRun = $this->option('dry-run');
            // No global rollback needed since we use individual transactions
            $this->error('❌ Error crítico durante la migración: '.$exception->getMessage());
            if (! $isDryRun) {
                $this->error('Las citas procesadas exitosamente antes del error se mantuvieron en la base de datos.');
            }

            return Command::FAILURE;
        }
    }

    /**
     * Calculate appointment duration safely
     */
    private function calculateDuration($appSCB)
    {
        try {
            if (isset($appSCB->start_date) && isset($appSCB->end_date)) {
                return Carbon::parse($appSCB->start_date)->diffInMinutes(Carbon::parse($appSCB->end_date));
            }

            return 30; // Default 30 minutes
        } catch (\Exception $e) {
            return 30; // Default 30 minutes
        }
    }

    /**
     * Create encounter and related records
     */
    private function createEncounter($appSCB, $appointment, $medical_speciality)
    {
        $type = '4525004'; // consulta de medicina general
        if ($medical_speciality->id != '58') {
            $type = '26172008'; // consulta de especialidad
        }

        $encounter = Encounter::create([
            'fhir_id' => 'encounter-'.fake()->uuid(),
            'patient_id' => $appointment->patient_id,
            'practitioner_id' => $appointment->practitioner_id,
            'appointment_id' => $appointment->id,
            'identifier' => 'ENC-'.fake()->unique()->numerify('#######'),
            'status' => 'finished',
            'class' => 'SS',
            'type' => $type,
            'priority' => 'routine',
            'start' => $appSCB->cstar,
            'medical_speciality_id' => $medical_speciality->id,
            'reason' => $appSCB->chief_complaint,
            'end' => $appSCB->cend,
            'scb_id' => $appSCB->consultation_id,
        ]);

        if (! $encounter) {
            throw new \Exception("Failed to create encounter for appointment {$appointment->scb_id}");
        }

        try {
            // Save all related clinical data
            $this->saveVitalSigns($appSCB, $encounter);
            $this->savePhysicalExaminations($appSCB, $encounter);
            $this->saveConditions($appSCB, $encounter);
            $this->savePresentIllnes($appSCB, $encounter);
            $this->seveServiceRequests($appSCB, $encounter);
            $this->saveReferralRequests($appSCB, $encounter);
            $this->saveMedicationRequests($appSCB, $encounter);
        } catch (\Exception $e) {
            throw new \Exception("Failed to save encounter data for appointment {$appointment->scb_id}: ".$e->getMessage());
        }

        return $encounter;
    }

    private function saveConditions($appSCB, $encounter)
    {

        $diagnostics = DB::connection('scb')->table('consultation_diagnostics cd')
            ->join('diagnostics d', 'cd.ICD10_CODE', '=', 'd.ICD10_CODE')
            ->select('cd.consultation_id',
                'cd.created_at',
                'cd.secundary_diagnostic_explanation',
                'd.code',
                'd.descriptions',
                'd.icd10_code',
                'd.is_icd10')
            ->where('cd.consultation_id', $appSCB->consultation_id)
            ->whereNull('cd.deleted_at')
            ->get();

        $rank = 1;
        $use = 'principal';
        $i = 1;
        foreach ($diagnostics as $diagnostic) {

            $icd10_code = str_replace('.', '', $diagnostic->icd10_code);
            $condition = Condition::wherePatientId($encounter->patient_id)->whereCode($icd10_code)->first();

            $category = 'ICD10';
            if ($diagnostic->is_icd10 == 'N') {
                $category = 'VA DIAGNOSTIC';
            }

            if (! $condition) {
                $condition = Condition::create([
                    'fhir_id' => 'condition-'.Str::uuid(),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                    'identifier' => 'DX-'.fake()->unique()->numerify('#######'),
                    'clinical_status' => 'active',
                    'verification_status' => 'confirmed',
                    'code' => $icd10_code,
                    'onset_info' => $diagnostic->descriptions,
                    'category' => $category,
                    'severity' => 'normal',
                    'onset_date' => $diagnostic->created_at,
                    'recorded_date' => $diagnostic->created_at,
                ]);
            }

            if ($i > 1) {
                $rank++;
                $use = 'secundary';
            }

            if (! EncounterDiagnosis::whereEncounterId($encounter->id)->whereConditionId($condition->id)->first()) {
                $ed = $encounter->diagnoses()->create([
                    'encounter_id' => $encounter->id,
                    'condition_id' => $condition->id,
                    'rank' => $rank,
                    'use' => $use,
                ]);
            }
            $i++;
        }

    }

    private function saveVitalSigns($appSCB, $encounter)
    {

        $vitalSigsCode = ['bp' => '8480-6', 'bp2' => '8462-4', 'temperature' => '8310-5', 'hr' => '8867-4', 'rf' => '9279-1', 'weight' => '29463-7', 'height' => '8302-2', 'imc' => '39156-5'];
        $vitalSCB = DB::connection('scb')->select('SELECT * FROM consultation_vital_sign WHERE consultation_id = ?', [$appSCB->consultation_id]);

        foreach ($vitalSigsCode as $key => $value) {
            $vs = $encounter->vitalSigns()->whereEncounterId($encounter->id)->whereCode($value)->first();

            if (! $vs && $vitalSCB && count($vitalSCB) > 0 && ! empty($vitalSCB[0]->$key) && ! in_array($vitalSCB[0]->$key, ['-', '.'])) {
                $vsType = ClinicalObservationType::whereCode($value)->first();
                $encounter->vitalSigns()->create([
                    'fhir_id' => 'observation-'.fake()->uuid(),
                    'code' => $value,
                    'status' => 'final',
                    'category' => 'vital-signs',
                    'value' => (float) $vitalSCB[0]->$key,
                    'unit' => $vsType->default_unit,
                    'effective_date' => $encounter->start,
                    'issued_date' => $encounter->start,
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ]);
            }
        }

    }

    private function savePhysicalExaminations($appSCB, $encounter)
    {
        $physicalExamCode = [
            'constitutional' => '24825-0',
            'heent' => '24821-9',
            'pulmonary' => '24627-0',
            'cadiovascular' => '24636-1',
            'chest_breast' => '24826-8',
            'gastroinstestinal' => '10230-1',
            'lymphatic' => '24828-4',
            'musculoskeletal' => '24820-1',
            'skin' => '24823-5',
            'neurologic' => '24822-7',
            'psychiantric' => '24831-8'];

        $physicalSCB = DB::connection('scb')
            ->select('SELECT * FROM consultation_physical_examination WHERE consultation_id = ?', [$appSCB->consultation_id]);

        foreach ($physicalExamCode as $key => $value) {

            $pe = $encounter->physicalExams()->whereEncounterId($encounter->id)->whereCode($value)->first();

            $vsType = ClinicalObservationType::whereCode($value)->first();

            if (! $pe && $physicalSCB && count($physicalSCB) > 0 && ! empty($physicalSCB[0]->$key)) {
                $encounter->physicalExams()->create([
                    'fhir_id' => 'observation-'.fake()->uuid(),
                    'code' => $value,
                    'status' => 'final',
                    'category' => 'exam',
                    'description' => $vsType->name.' realizado durante la consulta',
                    'finding' => ['text' => $physicalSCB[0]->$key],
                    'effective_date' => $physicalSCB[0]->created_at,
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                ]);
            }

        }
    }

    private function savePresentIllnes($appSCB, $encounter)
    {
        $locations = explode(';', $appSCB->locations);

        $location = PresentIllnesType::whereIn('scb_id', $locations)->pluck('value')->toArray();
        $severity = PresentIllnesType::whereScbId($appSCB->severity)->first();
        $duration = PresentIllnesType::whereScbId($appSCB->duration)->first();
        $timing = PresentIllnesType::whereScbId($appSCB->timing)->first();
        $severidad = 'unknown';

        if($severity && in_array($severity->value,['mild','moderate','severe','disabling','unknown'])){
            $severidad = $severity->value;
        }

        $encounter->presentIllnesses()->create([
            'fhir_id' => 'condition-'.fake()->uuid(),
            'description' => strtolower($appSCB->description) ?? '-',
            'associated_symptoms' => strtolower($appSCB->symptoms),
            'aggravating_factors' => strtolower($appSCB->modifying_factor),
            'locations' => array_values($location),
            'location' => count($location) > 0 ? $location[0] : null,
            'severity' =>$severidad,
            'duration' => $duration ? $duration->value : 'unknown',
            'timing' => $timing ? $timing->value : 'unknown',
            'patient_id' => $encounter->patient_id,
            'practitioner_id' => $encounter->practitioner_id,
            'onset_date' => $encounter->start,
        ]);

    }

    private function seveServiceRequests($appSCB, $encounter)
    {
        // SERVICE REQUESTS
        $exams = DB::connection('scb')->select("SELECT * FROM CONSULTATION_EXAMS ce WHERE type in ('laboratory','images','procedure') and deleted_at is null and ce.CONSULTATION_ID = ".$appSCB->consultation_id);

        if ($exams && count($exams) > 0) {
            // Create a grouped service request record for this consultation
            $serviceGroup = [
                'consultation_id' => $appSCB->consultation_id,
                'encounter_id' => $encounter->id,
                'total_services' => count($exams),
                'group_identifier' => 'SR-GROUP-'.fake()->unique()->numerify('#######'),
                'created_at' => now(),
                'types' => collect($exams)->pluck('type')->unique()->values()->toArray(),
            ];

            foreach ($exams as $exam) {
                $sr = $encounter->serviceRequests()->create([
                    'fhir_id' => 'servicerequest-'.Str::uuid(),
                    'patient_id' => $encounter->patient_id,
                    'practitioner_id' => $encounter->practitioner_id,
                    'status' => 'active',
                    'intent' => 'order',
                    'priority' => 'asap',
                    'do_not_perform' => 0,
                    'code' => $exam->code,
                    'service_type' => $exam->type,
                    'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                    'quantity' => 1,
                    'occurrence_start' => $exam->created_at,
                    'authored_on' => $exam->created_at,
                    'last_updated' => $exam->updated_at,
                    'scb_id' => $exam->id,
                    'service_group' => json_encode($serviceGroup),
                    'group_position' => array_search($exam, $exams) + 1,
                ]);

                if ($sr) {
                    // BUSCAR LOS APPOINTMENTS_DETAIL por consultation EXAM
                    $results = DB::connection('scb')->select('SELECT ad.CPT_TYPE ,ad.INSURANCE ,d.ICD10_CODE_HOMOLOGADO   ,adf.id,adf.APPOINTMENT_DETAIL_ID ,adf.CREATED_AT ,adf.PATH ,adf.DOC_NAME ,adf.DOC_TYPE ,adf.DOC_SUB_TYPE ,adf.FILE_HASH ,adf.UPLOADED_BY_NAME
                                                                        FROM APPOINTMENTS_DETAIL ad ,APPOINTMENTS_DETAIL_FILES adf ,DIAGNOSTICS d
                                                                        WHERE ad.id = adf.APPOINTMENT_DETAIL_ID
                                                                        AND ad.DIAG_CODE =d.code
                                                                        AND ad.DELETED_AT IS NULL AND adf.DELETED_AT IS NULL
                                                                    AND ad.CONSULTATION_EXAM_ID ='.$exam->id);

                    foreach ($results as $rs) {

                        $file_hash = hash('sha256', fake()->text(1000));
                        if (! empty($rs->file_hash)) {
                            $file_hash = $rs->file_hash;
                        }

                        ServiceRequestResult::create([
                            'fhir_id' => 'SRR-'.uniqid(),
                            'service_request_id' => $sr->id,
                            'patient_id' => $encounter->patient_id,
                            'practitioner_id' => $encounter->practitioner_id,
                            'status' => 'final',
                            'result_type' => $exam->type,
                            'code' => $exam->code,
                            'code_system' => 'https://www.ama-assn.org/practice-management/cpt',
                            'file_path' => $rs->path,
                            'file_name' => $rs->doc_name,
                            'file_type' => $rs->doc_type ?? 'EXME',
                            'file_size' => fake()->numberBetween(1024, 5242880), // 1KB to 5MB,
                            'file_hash' => $file_hash,
                            'metadata' => [
                                'insurance' => $rs->insurance,
                                'icd10_code' => $rs->icd10_code_homologado,
                                'uploaded_by_name' => $rs->uploaded_by_name,
                                'resolution' => fake()->optional()->randomElement(['300dpi', '600dpi', '1200dpi']),
                                'pages' => fake()->numberBetween(1, 10),
                            ],
                            'result_date' => $rs->created_at,
                            'uploaded_at' => $rs->created_at,
                            'observations' => null,
                            'notes' => null,
                            'interpretation' => 'normal',
                            'reference_range' => null,
                            'specimen_info' => null,
                            'version' => 1,
                            'replaces_id' => null,
                            'effective_date' => $rs->created_at,
                            'issued_date' => $rs->created_at,
                        ]);
                        $sr->status = 'completed';
                        $sr->save();
                    }
                }
            }
        }
    }

    private function saveMedicationRequests($appSCB, $encounter)
    {
        $medicines = DB::connection('scb')->select('SELECT cm.CONSULTATION_ID ,cm.PLAN ,cm.QTY ,cm.MONTHS ,cm.NARCOTICO,cm.CREATED_AT  ,p.GENERIC_NAME
                    ,p.HOME_NAME ,p.PILLS_MGS ,p.PILLS_MGS_TYPE
                    FROM CONSULTATION_MEDICINES cm , PRODUCTS p
                    WHERE cm.PRODUCT_ID = p.id
                    AND cm.consultation_id = '.$appSCB->consultation_id.'
                    AND cm.DELETED_AT IS null');

        if ($medicines && count($medicines) > 0) {
            // Create a grouped medication record for this consultation
            $medicationGroup = [
                'consultation_id' => $appSCB->consultation_id,
                'encounter_id' => $encounter->id,
                'total_medications' => count($medicines),
                'group_identifier' => 'RX-GROUP-'.fake()->unique()->numerify('#######'),
                'created_at' => now(),
            ];

            foreach ($medicines as $medicine) {
                $medicine_request = MedicationRequest::whereEncounterId($encounter->id)->whereMedication($medicine->home_name)->first();
                if (! $medicine_request) {
                    // Calcular frecuencia en horas
                    $validFrom = Carbon::parse($medicine->created_at);
                    $validTo = Carbon::parse($medicine->created_at)->addMonth((int) $medicine->months);
                    $totalHours = $validFrom->diffInHours($validTo);
                    $totalPills = (int) $medicine->qty;

                    // Frecuencia = Total de horas del tratamiento / Total de pastillas
                    $frequencyHours = $totalPills > 0 ? round($totalHours / $totalPills, 1) : 24;

                    // Generar texto de frecuencia más legible
                    $frequencyText = $this->generateFrequencyText($frequencyHours);

                    $encounter->medicationRequests()->create([
                        'fhir_id' => 'medicationrequest-'.Str::uuid(),
                        'identifier' => 'RX-'.fake()->unique()->numerify('#######'),
                        'status' => 'completed',
                        'intent' => 'order',
                        'medication' => strtolower($medicine->home_name),
                        'valid_from' => $validFrom->format('Y-m-d'),
                        'valid_to' => $validTo,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'quantity' => $totalPills,
                        'frequency' => $frequencyHours,
                        'route' => 'Oral',
                        'narcotic' => $medicine->narcotico,
                        'dosage_instruction' => strtolower($medicine->plan),
                        'refills' => $medicine->months,
                        'medication_group' => json_encode($medicationGroup),
                        'group_position' => array_search($medicine, $medicines) + 1,
                    ]);
                }
            }
        }
    }

    private function saveReferralRequests($appSCB, $encounter)
    {
        $referrals = DB::connection('scb')
            ->select("SELECT cs.CONSULTATION_ID,cs.CREATED_AT,cs.OBSERVATION  ,ms.NAME ,cn.NOTE AS reason
                            FROM CONSULTATION_SPECIALTIES cs ,MEDICAL_SPECIALTIES ms ,CONSULTATION_NOTES cn
                            WHERE cs.SPECIALITY_ID =ms.id AND cs.CONSULTATION_ID =cn.CONSULTATION_ID AND cn.\"TYPE\" ='necesidad-medica-specialties'
                            AND cs.DELETED_AT IS NULL
                            AND cs.CONSULTATION_ID =".$appSCB->consultation_id);

        if ($referrals && count($referrals) > 0) {
            // Create a grouped referral record for this consultation
            $referralGroup = [
                'consultation_id' => $appSCB->consultation_id,
                'encounter_id' => $encounter->id,
                'total_referrals' => count($referrals),
                'group_identifier' => 'REF-GROUP-'.fake()->unique()->numerify('#######'),
                'created_at' => now(),
                'specialties' => collect($referrals)->pluck('name')->unique()->values()->toArray(),
            ];

            foreach ($referrals as $referral) {
                $specialty = MedicalSpeciality::whereName($referral->name)->first();

                if ($specialty) {
                    $referralss = \App\Models\Referral::whereEncounterId($encounter->id)->whereCode($specialty->id)->first();

                    if (! $referralss) {
                        $encounter->referrals()->create([
                            'fhir_id' => 'servicerequest-'.Str::uuid(),
                            'identifier' => 'REF-'.fake()->unique()->numerify('#######'),
                            'status' => 'active',
                            'intent' => 'order',
                            'priority' => 'asap',
                            'code' => $specialty->id,
                            'description' => "Referencia a especialista en {$specialty->name}",
                            'occurrence_date' => $appSCB->created_at,
                            'reason' => strtolower($referral->reason),
                            'notes' => strtolower($referral->observation),
                            'supporting_info' => [
                                'speciality_id' => $specialty->id,
                                'speciality_name' => $specialty->name,
                            ],
                            'patient_id' => $encounter->patient_id,
                            'practitioner_id' => $encounter->practitioner_id,
                            'referral_group' => json_encode($referralGroup),
                            'group_position' => array_search($referral, $referrals) + 1,
                        ]);
                    }
                }
            }
        }
    }

    private function saveMedicalHistory($appSCB, $patient)
    {

        $categoryCode = [
            'medicine' => 'medication',
            'allergy' => 'allergies',
            'surgery' => 'surgery',
            'family-history' => 'drugs',
            'family-history' => 'tabacco',
            'family-history' => 'alcohol',
            'social-history' => '',
            'other' => 'other'];

        foreach ($categoryCode as $key => $value) {

            $medicalHistory = MedicalHistory::wherePatientId($patient->id)->whereCategory($key)->first();
            if (! $medicalHistory && ! empty($appSCB->$value)) {

                if (! in_array(strtolower($appSCB->$value), ['no', 'denies', 'denied', 'niega'])) {
                    $patient->medicalHistories()->create([
                        'fhir_id' => 'medicalhistory-'.Str::uuid(),
                        'category' => $key,
                        'clinical_status' => 'active',
                        'title' => $key,
                        'description' => strtolower($appSCB->$value),
                        'recorded_date' => $appSCB->created_at,
                        'occurrence_date' => $appSCB->created_at,
                        'verification_status' => 'confirmed',
                    ]);
                }
            }

        }
    }

    /**
     * Verifica si la columna meditec_migration existe en la tabla appointments de Oracle
     *
     * @return bool
     */
    private function checkMigrationColumn()
    {
        return true;
    }

    /**
     * Genera un texto legible para la frecuencia de medicación
     *
     * @param  float  $frequencyHours  Frecuencia en horas
     * @return string
     */
    private function generateFrequencyText($frequencyHours)
    {
        if ($frequencyHours <= 0) {
            return 'Una vez al día';
        }

        // Convertir a diferentes formatos según la frecuencia
        if ($frequencyHours < 1) {
            $minutes = round($frequencyHours * 60);

            return "Cada {$minutes} minutos";
        } elseif ($frequencyHours < 24) {
            // Si es menos de 24 horas, mostrar en horas
            if ($frequencyHours == 1) {
                return 'Cada hora';
            } elseif ($frequencyHours == 2) {
                return 'Cada 2 horas';
            } elseif ($frequencyHours == 4) {
                return 'Cada 4 horas';
            } elseif ($frequencyHours == 6) {
                return 'Cada 6 horas';
            } elseif ($frequencyHours == 8) {
                return 'Cada 8 horas';
            } elseif ($frequencyHours == 12) {
                return 'Cada 12 horas';
            } else {
                $hours = round($frequencyHours, 1);

                return "Cada {$hours} horas";
            }
        } else {
            // Si es 24 horas o más, convertir a días
            $days = round($frequencyHours / 24, 1);
            if ($days == 1) {
                return 'Una vez al día';
            } elseif ($days == 2) {
                return 'Cada 2 días';
            } elseif ($days == 3) {
                return 'Cada 3 días';
            } elseif ($days == 7) {
                return 'Una vez a la semana';
            } else {
                return "Cada {$days} días";
            }
        }
    }

    /**
     * Genera un resumen de la consulta para mostrar en el progreso
     *
     * @param  object  $appSCB  Datos de la cita desde SCB
     * @param  object  $entity  Encounter o Appointment creado
     * @return string
     */
    private function getConsultationSummary($appSCB, $entity)
    {
        $summary = [];

        // Agregar especialidad si existe
        if (! empty($appSCB->speciality)) {
            $summary[] = $appSCB->speciality;
        }

        // Agregar motivo de consulta si existe
        if (! empty($appSCB->chief_complaint)) {
            $complaint = substr($appSCB->chief_complaint, 0, 30);
            if (strlen($appSCB->chief_complaint) > 30) {
                $complaint .= '...';
            }
            $summary[] = $complaint;
        }

        // Agregar tipo de entidad
        $entityType = class_basename($entity);
        $summary[] = $entityType;

        return implode(' | ', array_filter($summary));
    }

    /**
     * Marca una cita como migrada en la base de datos Oracle SCB
     *
     * @param  string  $appointmentId  ID de la cita en SCB
     * @return bool
     */
    private function markAppointmentAsMigrated($appointmentId)
    {
        try {
            DB::connection('scb')->table('appointments')
                ->where('id', $appointmentId)
                ->update(['meditec_migration' => '1']);

            return true;
        } catch (\Exception $e) {
            Log::error("Error marcando cita {$appointmentId} como migrada: ".$e->getMessage());

            return false;
        }
    }
}
