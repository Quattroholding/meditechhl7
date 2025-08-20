<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\PatientInsurancePolicy;
use App\Models\User;
use Database\Factories\PatientInsurancePolicyFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigratePatientsFromSCB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-patients-from-scb
                            {--dry-run : Ejecutar en modo simulación sin guardar cambios}
                            {--batch-size=100 : Número de pacientes a procesar por lote}
                            {--limit= : Limitar el número total de pacientes a migrar}
                            {--force-remigrate : Ignorar flag de migración y procesar todos los pacientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para ver como migrar los pacientes de SCB oracle la version anterior a la version nueva';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $isDryRun = $this->option('dry-run');
            $batchSize = (int) $this->option('batch-size', 100);
            $limit = $this->option('limit') ? (int) $this->option('limit') : null;
            $forceRemigrate = $this->option('force-remigrate');

            if ($isDryRun) {
                $this->warn('🧪 Ejecutando en modo DRY-RUN - No se guardarán cambios');
            }

            if ($forceRemigrate) {
                $this->warn('⚠️  Modo FORCE-REMIGRATE activado - Se procesarán todos los pacientes');
            }

            $this->info('🔄 Iniciando migración de pacientes desde SCB...');

            // Obtener el conteo total de pacientes para el progress bar
            $query = DB::connection('scb')->table('patients')
                ->whereActiveInactive('S')
                ->whereAlive('Y')
                ->whereNotNull('e_mail');

            // Solo pacientes no migrados, a menos que se fuerce la re-migración
            if (!$forceRemigrate) {
                $query->where('meditec_migration', '0');
            }

            if ($limit) {
                $query->limit($limit);
            }

            $totalCount = $query->count();

            if ($totalCount === 0) {
                $this->warn('⚠️  No se encontraron pacientes para migrar.');
                return Command::SUCCESS;
            }

            $this->info("📊 Se encontraron {$totalCount} pacientes para migrar.");

            if (!$this->confirm('¿Desea continuar con la migración?')) {
                $this->info('❌ Migración cancelada por el usuario.');
                return Command::SUCCESS;
            }

            // Obtener los pacientes en lotes para mejor rendimiento
            $patientsQuery = DB::connection('scb')->table('patients')
                ->whereActiveInactive('S')
                ->whereAlive('Y')
                ->whereNotNull('e_mail');

            // Solo pacientes no migrados, a menos que se fuerce la re-migración
            if (!$forceRemigrate) {
                $patientsQuery->where('meditec_migration', '0');
            }

            if ($limit) {
                $patientsQuery->limit($limit);
            }

            $patients = $patientsQuery->get();

            // Crear el progress bar
            $progressBar = $this->output->createProgressBar($totalCount);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% - %message%');
            $progressBar->setMessage('Iniciando migración...');

            $this->line('');
            $progressBar->start();

            if (!$isDryRun) {
                DB::beginTransaction();
            }

            $migratedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            foreach ($patients as $patientSCB) {
                $progressBar->setMessage("Procesando: {$patientSCB->name} {$patientSCB->surrname}");
                try {
                    if(!Patient::whereScbId($patientSCB->id)->first()){
                    $password = 'password';

                    // DEBO CREAR UN PASSWORD GENÉRICO
                    $model = new User;
                    $model->first_name = $patientSCB->name;
                    $model->last_name = $patientSCB->surrname;
                    if(!User::whereEmail($patientSCB->e_mail)->first()){
                        $model->email = strtolower($patientSCB->e_mail);
                    }else{
                        $model->email = fake()->unique()->safeEmail;
                    }

                    $model->password =$password;
                    $model->whatsapp_phone = $patientSCB->whatsapp;

                    if (!$isDryRun) {
                        $model->save();
                    }

                    // Asignar rol de paciente
                    if (!$isDryRun) {
                        $model->assignRole('paciente');
                    }
                    $gender ='male';
                    $country = 'Panama';
                    if($patientSCB->sex=='F') $gender='female';
                    if($patientSCB->state==5) $country='Colombia';
                    if($patientSCB->state==7) $country='Ecuador';

                    switch (strtolower($patientSCB->patient_status)) {
                        case 'married':
                            $marital_status = 'Casado/a';
                            break;
                        case 'divorced':
                            $marital_status = 'Divorciado/a';
                            break;
                        case 'single':
                            $marital_status = 'Soltero/a';
                            break;
                        case 'widower':
                            $marital_status = 'Viudo/a';
                            break;
                        case 'other':
                            $marital_status = 'Otro/a';
                            break;
                    }

                    $patient = new Patient;
                    $patient->given_name = $patientSCB->name;
                    $patient->family_name = $patientSCB->surrname;
                    $patient->email = $patientSCB->e_mail;
                    $patient->phone = $patientSCB->phone;
                    $patient->whatsapp_phone = $patientSCB->whatsapp;
                    $patient->name = $patientSCB->name.' '.$patientSCB->surrname;
                    $patient->user_id = $model->id;
                    $patient->gender = $gender;
                    $patient->birth_date = $patientSCB->birthdate;
                    $patient->fhir_id = 'patient-'.Str::uuid();
                    $patient->communication = json_encode(['language' => 'en', 'preferred' => true]);
                    $patient->address = $patientSCB->address;
                    $patient->marital_status = $marital_status;
                    $patient->blood_type = $patientSCB->blood_type;
                    // IDENTIFIER ES ID
                    $patient->identifier_type = 'SS';

                    if(!Patient::whereIdentifier($patientSCB->us_ssnumber)->first()){
                        $patient->identifier = strtoupper($patientSCB->us_ssnumber);
                    }else{
                        $patient->identifier = strtoupper($patientSCB->us_ssnumber).'-'.strtoupper($patientSCB->relationship);
                    }
                    $patient->scb_id = $patientSCB->id;

                    $patientSaved = $isDryRun || $patient->save();
                    if ($patientSaved) {

                        $suscriber_patient_id=$patient->id;
                        $insurance_company_id = 1;
                        $priority = 'primary';
                        $relationship_to_subscriber ='self';
                        if(!empty($patientSCB->relationship))   $relationship_to_subscriber =strtolower($patientSCB->relationship);
                        $suscriber_name = $patient->name;
                        if($patientSCB->patient_type=='CHAMPUS')  $insurance_company_id = 2;

                        $policy_number = strtoupper($patientSCB->us_ssnumber);
                        if($patientSCB->id <> $patientSCB->sponsor_id){
                            $sponsor = DB::connection('scb')->table('patients')->where('id', $patientSCB->sponsor_id)->first();

                            if($sponsor){
                                $policy_number = strtoupper($sponsor->us_ssnumber);
                                $paSponsor = Patient::whereScbId($sponsor->id)->first();
                                if($paSponsor)
                                    $suscriber_patient_id = $paSponsor->id;

                                $priority = 'secondary';
                                $suscriber_name = $sponsor->name.' '.$sponsor->surrname;
                            }
                        }

                        if (!$isDryRun) {
                            PatientInsurancePolicy::factory()->create([
                            'patient_id'=>$patient->id,
                            'insurance_company_id'=>$insurance_company_id,
                            'policy_number'=> $policy_number,
                            'relationship_to_subscriber'=>$relationship_to_subscriber,
                            'subscriber_id'=> $policy_number,
                            'subscriber_name'=>$suscriber_name,
                            'subscriber_patient_id'=>$suscriber_patient_id,
                            'priority'=>$priority,
                            'is_active'=>1,
                            'coverage_percentage' => 100.00, // La mayoría de los servicios cubiertos al 100% (sin deducible tradicional)
                            'copay_amount' => 0.00, // Muchos servicios esenciales no tienen copago (depende del grupo de prioridad)
                            'deductible_amount' => 0.00, // VA no funciona con deducibles clásicos como los seguros privados
                            'deductible_remaining' => 0.00,
                            'out_of_pocket_max' => 0.00, // Generalmente no aplica límite máximo de bolsillo
                            'out_of_pocket_remaining' => 0.00,
                            'coverage_details' => [
                                'preventive_care' => true, // Siempre cubierto
                                'specialist_visits' => true, // Cubierto dentro del sistema VA o referido
                                'emergency_room' => true, // Cubierto en VA y en red comunitaria
                                'hospitalization' => true, // Cubierto
                                'prescription_drugs' => true, // Cubiertos, con copagos bajos o nulos
                                'mental_health' => true, // Cubierto, con exención de copagos para las primeras 3 visitas ambulatorias hasta 2027
                                'physical_therapy' => true, // Incluido en rehabilitación VA
                                'dental' => false, // Solo en casos específicos (discapacidad 100%, prisionero de guerra, etc.)
                                'vision' => true, // Exámenes cubiertos, gafas solo en criterios específicos
                            ],
                            'notes' => 'Cobertura médica del VA: la mayoría de los servicios médicos y hospitalarios están incluidos sin deducibles. Dental solo cubierto en casos específicos; visión limitada a exámenes y gafas bajo ciertos criterios.',
                            ]);
                        }

                        // Actualizar flag en Oracle para marcar como migrado
                        if (!$isDryRun) {
                            $this->markPatientAsMigrated($patientSCB->id);
                        }

                        $migratedCount++;
                        $progressBar->setMessage("✅ Paciente migrado: {$patient->name}");
                    }
                } else {
                    $skippedCount++;
                    $progressBar->setMessage("⏭️  Paciente ya existe: {$patientSCB->name} {$patientSCB->surrname}");
                }

                } catch (\Exception $e) {
                    $errorCount++;
                    $progressBar->setMessage("❌ Error procesando paciente: {$patientSCB->name}");
                    // Log the error but continue with the migration
                    Log::error("Error migrando paciente {$patientSCB->id}: " . $e->getMessage());
                }

                $progressBar->advance();

                // Add a small delay to make the progress visible (optional)
                usleep(10000); // 10ms
            }

            $progressBar->finish();
            $this->line('');
            $this->line('');

            if (!$isDryRun) {
                DB::commit();
                $this->info('✅ Cambios guardados en la base de datos.');
            } else {
                $this->info('🧪 Simulación completada - No se guardaron cambios.');
            }

            // Show final summary
            $this->info('✅ Migración completada exitosamente!');

            $summaryData = [
                ['Total de pacientes procesados', $totalCount],
                ['Pacientes migrados', $migratedCount],
                ['Pacientes omitidos (ya existían)', $skippedCount],
                ['Errores encontrados', $errorCount],
            ];

            if ($forceRemigrate) {
                $summaryData[] = ['Modo force-remigrate', 'SÍ'];
            } else {
                $summaryData[] = ['Solo pacientes no migrados', 'SÍ'];
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
            if (!$isDryRun) {
                DB::rollBack();
                $this->error('❌ Error crítico durante la migración: ' . $exception->getMessage());
                $this->error('La migración ha sido revertida.');
            } else {
                $this->error('❌ Error durante la simulación: ' . $exception->getMessage());
            }

            return Command::FAILURE;
        }
    }

    /**
     * Marca un paciente como migrado en la base de datos Oracle SCB
     *
     * @param int $patientId ID del paciente en SCB
     * @return bool
     */
    private function markPatientAsMigrated($patientId)
    {
        try {
            DB::connection('scb')->table('patients')
                ->where('id', $patientId)
                ->update(['meditec_migration' => '1']);

            return true;
        } catch (\Exception $e) {
            Log::error("Error marcando paciente {$patientId} como migrado: " . $e->getMessage());
            return false;
        }
    }
}
