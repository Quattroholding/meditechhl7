<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\PatientRelationship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterPatientRelationships extends Command
{
    protected $signature = 'patients:register-relationships
                            {--dry-run : Ejecutar en modo simulación sin guardar cambios}
                            {--batch-size=100 : Número de pacientes a procesar por lote}
                            {--limit= : Limitar el número total de pacientes a procesar}
                            {--force : Forzar recreación de relaciones existentes}';

    protected $description = 'Registrar relaciones de pacientes basándose en el patrón del identifier (XXX-XX-XXXX-RELATIONSHIP)';

    // Mapping de códigos de relación encontrados en identifiers a códigos FHIR
    const RELATIONSHIP_MAPPING = [
        'SPOUSE' => 'SPOUSE',
        'CHILD' => 'CHILD',
        'CHILD2' => 'CHILD',
        'CHILD3' => 'CHILD',
        'CHILD4' => 'CHILD',
        'CHILD5' => 'CHILD',
        'PARENT' => 'PARENT',
        'FATHER' => 'PARENT',
        'MOTHER' => 'PARENT',
        'SIBLING' => 'SIBLING',
        'BROTHER' => 'SIBLING',
        'SISTER' => 'SIBLING',
        'GRANDPARENT' => 'GRANDPRN',
        'GRANDCHILD' => 'GRANDCHILD',
        'UNCLE' => 'UNCLE',
        'AUNT' => 'AUNT',
        'NEPHEW' => 'NEPHEW',
        'NIECE' => 'NIECE',
        'COUSIN' => 'COUSIN',
        'INLAW' => 'INLAW',
        'FRIEND' => 'FRND',
        'GUARDIAN' => 'GUARD',
        'OTHER' => 'O',
    ];

    public function handle()
    {
        try {
            $isDryRun = $this->option('dry-run');
            $batchSize = (int) $this->option('batch-size', 100);
            $limit = $this->option('limit') ? (int) $this->option('limit') : null;
            $force = $this->option('force');

            if ($isDryRun) {
                $this->warn('🧪 Ejecutando en modo DRY-RUN - No se guardarán cambios');
            }

            if ($force) {
                $this->warn('⚠️  Modo FORCE activado - Se recrearán relaciones existentes');
            }

            $this->info('👥 Iniciando registro de relaciones de pacientes desde identifiers...');

            // Query para pacientes con identifiers que contengan relaciones
            $query = Patient::whereRaw("identifier REGEXP '^[^-]+-[^-]+-[^-]+-[A-Za-z0-9]+$'")
                ->whereNotNull('identifier')
                ->where('identifier', '!=', '');

            if ($limit) {
                $query->limit($limit);
            }

            $totalCount = $query->count();

            if ($totalCount === 0) {
                $this->warn('⚠️  No se encontraron pacientes con patrones de relación en sus identifiers.');

                return Command::SUCCESS;
            }

            $this->info("📊 Se encontraron {$totalCount} pacientes con potenciales relaciones.");

            // Mostrar ejemplos de identifiers encontrados
            /*$examples = $query->limit(5)->pluck('identifier')->toArray();
            $this->info('📋 Ejemplos de identifiers encontrados:');
            foreach ($examples as $example) {
                $this->line("   - {$example}");
            }*/

            if (! $this->confirm('¿Desea continuar con el registro de relaciones?')) {
                $this->info('❌ Proceso cancelado por el usuario.');

                return Command::SUCCESS;
            }

            // Obtener pacientes
            $patients = $query->get();

            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalCount);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% - %message%');
            $progressBar->setMessage('Iniciando procesamiento de relaciones...');

            $this->line('');
            $progressBar->start();

            if (! $isDryRun) {
                DB::beginTransaction();
            }

            $relationshipsCreated = 0;
            $relationshipsUpdated = 0;
            $relationshipsSkipped = 0;
            $errorCount = 0;
            $loop_index = 0;

            foreach ($patients as $patient) {
                $progressBar->setMessage("Procesando: {$patient->name}");

                try {
                    $relationshipInfo = $this->extractRelationshipFromIdentifier($patient->identifier);

                    if (! $relationshipInfo) {
                        $relationshipsSkipped++;
                        $progressBar->setMessage("⏭️  Sin relación válida: {$patient->name}");
                        $progressBar->advance();

                        continue;
                    }

                    // Buscar paciente principal (holder) por identifier base
                    $holderPatient = Patient::where('identifier', $relationshipInfo['base_identifier'])
                        ->where('id', '!=', $patient->id)
                        ->first();

                    if (! $holderPatient) {
                        $relationshipsSkipped++;
                        $progressBar->setMessage("⏭️  Titular no encontrado: {$patient->name}");
                        $progressBar->advance();

                        continue;
                    }

                    // Verificar si la relación ya existe
                    $existingRelationship = PatientRelationship::where('patient_id', $holderPatient->id)
                        ->where('related_patient_id', $patient->id)
                        ->first();

                    if ($existingRelationship && ! $force) {
                        $relationshipsSkipped++;
                        $progressBar->setMessage("⏭️  Relación ya existe: {$patient->name}");
                        $progressBar->advance();

                        continue;
                    }

                    if (! $isDryRun) {
                        if ($existingRelationship && $force) {
                            // Actualizar relación existente
                            $existingRelationship->update([
                                'relationship_code' => $relationshipInfo['relationship_code'],
                                'relationship_display' => $relationshipInfo['relationship_display'],
                                'relationship_system' => 'http://terminology.hl7.org/CodeSystem/v3-RoleCode',
                                'is_active' => true,
                                'effective_date' => now(),
                            ]);
                            $relationshipsUpdated++;
                        } else {
                            // Crear nueva relación
                            PatientRelationship::create([
                                'fhir_id' => 'related-person-'.Str::uuid(),
                                'patient_id' => $holderPatient->id,
                                'related_patient_id' => $patient->id,
                                'relationship_code' => $relationshipInfo['relationship_code'],
                                'relationship_display' => $relationshipInfo['relationship_display'],
                                'relationship_system' => 'http://terminology.hl7.org/CodeSystem/v3-RoleCode',
                                'is_active' => true,
                                'effective_date' => now(),
                                'is_emergency_contact' => false,
                                'is_insurance_subscriber' => false,
                            ]);
                            $relationshipsCreated++;
                        }
                    } else {
                        $relationshipsCreated++;
                    }

                    $progressBar->setMessage("✅ Relación procesada: {$patient->name} -> {$holderPatient->name}");
                } catch (\Exception $e) {
                    $errorCount++;
                    $progressBar->setMessage("❌ Error procesando: {$patient->name}");
                    Log::error("Error procesando relación para paciente {$patient->id}: ".$e->getMessage());
                }
                $loop_index++;
                $progressBar->advance();
                usleep(10000); // 10ms delay

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
            $this->info('✅ Registro de relaciones completado exitosamente!');

            $summaryData = [
                ['Total de pacientes procesados', $totalCount],
                ['Relaciones creadas', $relationshipsCreated],
                ['Relaciones actualizadas', $relationshipsUpdated],
                ['Pacientes omitidos', $relationshipsSkipped],
                ['Errores encontrados', $errorCount],
            ];

            if ($force) {
                $summaryData[] = ['Modo force', 'SÍ'];
            }

            if ($isDryRun) {
                $summaryData[] = ['Modo dry-run', 'SÍ'];
            }

            $this->table(['Métrica', 'Resultado'], $summaryData);

            if ($errorCount > 0) {
                $this->warn("⚠️  Se encontraron {$errorCount} errores durante el proceso. Revisa los logs para más detalles.");
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
                $this->error('❌ Error crítico durante el procesamiento: '.$exception->getMessage());
                $this->error('Los cambios han sido revertidos.');
            } else {
                $this->error('❌ Error durante la simulación: '.$exception->getMessage());
            }

            return Command::FAILURE;
        }
    }

    /**
     * Extrae la información de relación del identifier del paciente
     */
    private function extractRelationshipFromIdentifier(string $identifier): ?array
    {
        // Patrón: XXX-XX-XXXX-RELATIONSHIP
        $parts = explode('-', $identifier);

        if (count($parts) < 4) {
            return null;
        }

        // Extraer la relación (último segmento)
        $relationshipRaw = strtoupper(end($parts));

        // Normalizar relaciones como CHILD2, CHILD3, etc. a CHILD
        $relationshipNormalized = preg_replace('/^(CHILD)\d+$/', 'CHILD', $relationshipRaw);

        // Mapear a código FHIR
        $relationshipCode = self::RELATIONSHIP_MAPPING[$relationshipNormalized] ?? null;

        if (! $relationshipCode) {
            return null;
        }

        // Construir identifier base (sin el sufijo de relación)
        array_pop($parts); // Remover la relación
        $baseIdentifier = implode('-', $parts);

        return [
            'base_identifier' => $baseIdentifier,
            'relationship_raw' => $relationshipRaw,
            'relationship_code' => $relationshipCode,
            'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$relationshipCode] ?? $relationshipCode,
        ];
    }
}
