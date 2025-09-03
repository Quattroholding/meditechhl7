<?php

namespace App\Console\Commands;

use App\Actions\CreatePractitionerUserAction;
use App\Models\Client;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigratePhysiciansFromSCB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-physicians-from-scb
                            {--dry-run : Ejecutar en modo simulación sin guardar cambios}
                            {--batch-size=100 : Número de médicos a procesar por lote}
                            {--limit= : Limitar el número total de médicos a migrar}
                            {--force-remigrate : Ignorar flag de migración y procesar todos los médicos}
                            {--create-users : Crear usuarios inactivos para los practicantes migrados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar médicos/practicantes desde la base de datos SCB Oracle con progress bar y tracking';

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
            $createUsers = $this->option('create-users');

            if ($isDryRun) {
                $this->warn('🧪 Ejecutando en modo DRY-RUN - No se guardarán cambios');
            }

            if ($forceRemigrate) {
                $this->warn('⚠️  Modo FORCE-REMIGRATE activado - Se procesarán todos los médicos');
            }

            if ($createUsers) {
                $this->info('👥 Opción CREATE-USERS activada - Se crearán usuarios inactivos para los practicantes');
            }

            $this->info('👩‍⚕️ Iniciando migración de médicos/practicantes desde SCB...');

            // Check if migration column exists, if not, ignore migration tracking
            $migrationColumnExists = $this->checkMigrationColumn();

            // Build the base query
            if ($migrationColumnExists) {
                $baseQuery = "SELECT pv.code, pv.name, pv.surrname, pv.phone, pv.ADDRESS, pv.MEDICAL_SPECIALITY_ID,
                                     pv.MD_CODE, pv.IDENTIFICATION, pv.REGISTRO, pv.CORREO, c.PAIS,
                                     COALESCE(pv.meditec_migration, '0') as meditec_migration,
                                     count(a.id) as appointments_count
                              FROM PHYSICIANS pv
                              LEFT JOIN APPOINTMENTS a ON pv.CODE = a.DOCTOR_ID AND a.STATUS = 'cerrada'
                              LEFT JOIN CLINICS c ON pv.CLINIC_CODE = c.code
                              WHERE pv.CORREO IS NOT NULL";

                // Add migration filter unless force remigrate is enabled
                if (! $forceRemigrate) {
                    $baseQuery .= " AND COALESCE(pv.meditec_migration, '0') = '0'";
                }

                $baseQuery .= ' GROUP BY pv.code, pv.name, pv.surrname, pv.phone, pv.ADDRESS,
                                        pv.MEDICAL_SPECIALITY_ID, pv.MD_CODE, pv.IDENTIFICATION,
                                        pv.REGISTRO, pv.CORREO, c.PAIS, pv.meditec_migration';
            } else {
                $this->warn('⚠️  Columna meditec_migration no existe - Ejecutando sin tracking de migración');
                $baseQuery = "SELECT pv.code, pv.name, pv.surrname, pv.phone, pv.ADDRESS, pv.MEDICAL_SPECIALITY_ID,
                                     pv.MD_CODE, pv.IDENTIFICATION, pv.REGISTRO, pv.CORREO, c.PAIS,
                                     '0' as meditec_migration,
                                     count(a.id) as appointments_count
                              FROM PHYSICIANS pv
                              LEFT JOIN APPOINTMENTS a ON pv.CODE = a.DOCTOR_ID AND a.STATUS = 'cerrada'
                              LEFT JOIN CLINICS c ON pv.CLINIC_CODE = c.code
                              WHERE pv.CORREO IS NOT NULL
                              GROUP BY pv.code, pv.name, pv.surrname, pv.phone, pv.ADDRESS,
                                      pv.MEDICAL_SPECIALITY_ID, pv.MD_CODE, pv.IDENTIFICATION,
                                      pv.REGISTRO, pv.CORREO, c.PAIS";
            }

            // Add order by for consistent results
            $baseQuery .= ' ORDER BY pv.code';

            // Get total count first (without limit for accurate count)
            $countQuery = "SELECT COUNT(*) as total FROM (
                {$baseQuery}
            ) physicians_subquery";

            // If limit is specified, modify the base query to use Oracle ROWNUM
            if ($limit) {
                $baseQuery = "SELECT * FROM (
                    {$baseQuery}
                ) WHERE ROWNUM <= {$limit}";
            }

            $totalResult = DB::connection('scb')->select($countQuery);
            $totalCount = $totalResult[0]->total ?? 0;

            if ($totalCount === 0) {
                $this->warn('⚠️  No se encontraron médicos para migrar.');

                return Command::SUCCESS;
            }

            $this->info("📊 Se encontraron {$totalCount} médicos para migrar.");

            if (! $this->confirm('¿Desea continuar con la migración?')) {
                $this->info('❌ Migración cancelada por el usuario.');

                return Command::SUCCESS;
            }

            // Get the physicians data
            $scb_physicians = DB::connection('scb')->select($baseQuery);

            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalCount);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% - %message%');
            $progressBar->setMessage('Iniciando migración de médicos...');

            $this->line('');
            $progressBar->start();

            if (! $isDryRun) {
                DB::beginTransaction();
            }

            $migratedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $usersCreatedCount = 0;

            // Initialize user creation action if needed
            $userAction = null;
            $defaultClient = null;
            if ($createUsers && ! $isDryRun) {
                $userAction = new CreatePractitionerUserAction;
                $defaultClient = Client::first();

                if (! $defaultClient) {
                    $this->error('❌ No se encontró un cliente por defecto para crear usuarios');

                    return Command::FAILURE;
                }

                // Create a dummy admin user for context
                $adminUser = User::first();
                auth()->login($adminUser);

                $this->info("👤 Usuario por defecto establecido para creación: {$defaultClient->name}");
            }

            foreach ($scb_physicians as $physician) {
                $physicianName = "{$physician->name} {$physician->surrname}";
                $progressBar->setMessage("Procesando: Dr(a) {$physicianName}");

                try {
                    // Check if practitioner already exists in Laravel
                    $existingPractitioner = Practitioner::whereScbId($physician->code)->first();

                    if (! $existingPractitioner) {
                        $practitioner = new Practitioner;
                        $practitioner->fhir_id = 'practitioner-'.Str::uuid();
                        $practitioner->email = strtolower($physician->correo);
                        $practitioner->name = 'Dr(a) '.ucfirst(strtolower($physician->name)).' '.ucfirst(strtolower($physician->surrname));
                        $practitioner->given_name = ucfirst(strtolower($physician->name));
                        $practitioner->family_name = ucfirst(strtolower($physician->surrname));
                        $practitioner->identifier_type = 'CC';
                        $identifier = $physician->identification;
                        if (empty($identifier)) {
                            $identifier = fake()->unique()->regexify($this->getIdPattern('CC'));
                        }
                        $practitioner->identifier = $identifier;
                        $practitioner->registry = $physician->registro ?? fake()->unique()->numerify('#######');
                        $practitioner->birth_date = Carbon::now()->subYear(40);
                        $practitioner->address = strtolower($physician->address);
                        $practitioner->gender = fake()->randomElement(['male', 'female']);
                        $practitioner->phone = $physician->phone;
                        $practitioner->scb_id = $physician->code;
                        $practitioner->active = 0;

                        $practitionerSaved = $isDryRun || $practitioner->save();

                        if ($practitionerSaved) {
                            // Handle medical specialties
                            if (! $isDryRun && $physician->medical_speciality_id) {
                                $medical_speciality = MedicalSpeciality::find($physician->medical_speciality_id);
                                if ($medical_speciality) {
                                    $specialtyData = [
                                        $physician->medical_speciality_id => [
                                            'code' => $physician->medical_speciality_id,
                                            'default' => true,
                                            'medical_speciality_id' => $physician->medical_speciality_id,
                                            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                            'practitioner_id' => $practitioner->id,
                                            'display' => $medical_speciality->name,
                                        ],
                                    ];
                                    $practitioner->specialties()->sync($specialtyData);
                                }
                            }

                            // Create inactive user if option is enabled
                            if ($createUsers && $userAction && ! User::where('email', $practitioner->email)->exists()) {
                                try {
                                    $user = $userAction->execute($practitioner, false);
                                    $practitioner->user_id = $user->id;

                                    $practitioner->update(['user_id' => $user->id]);

                                    // Ensure user is associated with the default client
                                    if (! $user->clients()->where('client_id', $defaultClient->id)->exists()) {
                                        $user->clients()->attach($defaultClient->id);
                                    }

                                    if (! $user->default_client_id) {
                                        $user->update(['default_client_id' => $defaultClient->id]);
                                    }

                                    $usersCreatedCount++;
                                } catch (\Exception $userError) {
                                    Log::error("Error creando usuario para médico {$physician->code}: ".$userError->getMessage());
                                }
                            }

                            // Mark as migrated in Oracle
                            if (! $isDryRun && $migrationColumnExists) {
                                $this->markPhysicianAsMigrated($physician->code);
                            }

                            $migratedCount++;
                            $progressBar->setMessage("✅ Médico migrado: Dr(a) {$physicianName}");
                        }
                    } else {
                        $skippedCount++;
                        $progressBar->setMessage("⏭️  Médico ya existe: Dr(a) {$physicianName}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $progressBar->setMessage("❌ Error procesando médico: Dr(a) {$physicianName}");
                    // Log the error but continue with the migration
                    Log::error("Error migrando médico {$physician->code}: ".$e->getMessage());
                }

                $progressBar->advance();

                // Add a small delay to make the progress visible (optional)
                usleep(10000); // 10ms
            }

            $progressBar->finish();
            $this->line('');
            $this->line('');

            if (! $isDryRun) {
                DB::commit();
                $this->info('✅ Cambios guardados en la base de datos.');
            } else {
                $this->info('🧪 Simulación completada - No se guardaron cambios.');
            }

            // Show final summary
            $this->info('✅ Migración de médicos completada exitosamente!');

            $summaryData = [
                ['Total de médicos procesados', $totalCount],
                ['Médicos migrados', $migratedCount],
                ['Médicos omitidos (ya existían)', $skippedCount],
                ['Errores encontrados', $errorCount],
            ];

            if ($createUsers) {
                $summaryData[] = ['Usuarios creados (inactivos)', $usersCreatedCount];
            }

            if ($forceRemigrate) {
                $summaryData[] = ['Modo force-remigrate', 'SÍ'];
            } else {
                $summaryData[] = ['Solo médicos no migrados', 'SÍ'];
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
            if (! $isDryRun) {
                DB::rollBack();
                $this->error('❌ Error crítico durante la migración: '.$exception->getMessage());
                $this->error('La migración ha sido revertida.');
            } else {
                $this->error('❌ Error durante la simulación: '.$exception->getMessage());
            }

            return Command::FAILURE;
        }
    }

    /**
     * Verifica si la columna meditec_migration existe en la tabla physicians de Oracle
     *
     * @return bool
     */
    private function checkMigrationColumn()
    {
        try {
            $result = DB::connection('oracle')->select("
                SELECT COUNT(*) as column_exists
                FROM USER_TAB_COLUMNS
                WHERE TABLE_NAME = 'PHYSICIANS'
                AND COLUMN_NAME = 'MEDITEC_MIGRATION'
            ");

            return ($result[0]->column_exists ?? 0) > 0;
        } catch (\Exception $e) {
            Log::warning('No se pudo verificar la columna meditec_migration: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Marca un médico como migrado en la base de datos Oracle SCB
     *
     * @param  string  $physicianCode  Código del médico en SCB
     * @return bool
     */
    private function markPhysicianAsMigrated($physicianCode)
    {
        try {
            DB::connection('scb')->table('physicians')
                ->where('code', $physicianCode)
                ->update(['meditec_migration' => '1']);

            return true;
        } catch (\Exception $e) {
            Log::error("Error marcando médico {$physicianCode} como migrado: ".$e->getMessage());

            return false;
        }
    }

    private function getIdPattern($id_type)
    {
        switch ($id_type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456 o PE-123-456
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
            case 'CE': // Cédula Extranjera: Similar a CC
                return '/^[A-Z]*[0-9]+-[0-9]+-[0-9]+$/';
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
}
