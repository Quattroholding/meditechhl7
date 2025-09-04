<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Patient;
use App\Models\PatientClient;
use Illuminate\Console\Command;

class AddScbPatientToMeditecClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-scb-patient-to-client {--client-id=1 : ID del cliente al que se asignarán los pacientes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para cargar los pacientes migrados desde SCB y asignarlos a un cliente específico (por defecto cliente 1 Meditec)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientId = $this->option('client-id');

        $this->info('Iniciando migración de pacientes SCB...');
        $this->info("Cliente destino: {$clientId}");

        // Validar que el client_id existe
        $clientExists = Client::where('id', $clientId)->exists();
        if (!$clientExists) {
            $this->error("El cliente con ID {$clientId} no existe.");
            return 1;
        }

        // Obtener todos los pacientes con scb_id
        $patients = Patient::whereNotNull('scb_id')->get();
        $totalPatients = $patients->count();

        if ($totalPatients === 0) {
            $this->warn('No se encontraron pacientes con SCB ID.');
            return 0;
        }

        $this->info("Se encontraron {$totalPatients} pacientes para procesar.");

        // Crear progress bar
        $progressBar = $this->output->createProgressBar($totalPatients);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $processed = 0;
        $created = 0;
        $updated = 0;

        foreach ($patients as $patient) {
            try {
                $patientClient = PatientClient::updateOrCreate([
                    'patient_id' => $patient->id,
                    'client_id' => $clientId,
                ]);

                if ($patientClient->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                $processed++;

            } catch (\Exception $e) {
                $this->error("Error procesando paciente ID {$patient->id}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->info('Migración completada:');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total pacientes procesados', $processed],
                ['Registros creados', $created],
                ['Registros actualizados', $updated],
                ['Total en la base de datos', $totalPatients],
            ]
        );

        $this->info('✅ Comando ejecutado exitosamente.');

        return 0;
    }
}
