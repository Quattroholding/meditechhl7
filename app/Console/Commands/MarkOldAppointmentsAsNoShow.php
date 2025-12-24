<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Scopes\AppointmentScope;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkOldAppointmentsAsNoShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:mark-noshow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como noshow las citas propuestas, reservadas, pendientes o confirmadas que no se completaron después de 7 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fecha límite: hace 7 días
        $cutoffDate = Carbon::now()->subDays(7);

        $this->info("Buscando citas sin completar desde antes del {$cutoffDate->format('Y-m-d H:i:s')}...");

        // Buscar appointments que cumplan los criterios
        // Usamos withoutGlobalScope porque no hay usuario autenticado en comandos de consola
        $appointments = Appointment::withoutGlobalScope(AppointmentScope::class)
            ->whereIn('status', ['proposed', 'booked', 'pending', 'confirm'])
            ->where('start', '<', $cutoffDate)
            ->get();

        $count = $appointments->count();

        if ($count === 0) {
            $this->info('No se encontraron citas para marcar como noshow.');

            return 0;
        }

        $this->info("Se encontraron {$count} citas que serán marcadas como noshow...");

        // Barra de progreso
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;

        foreach ($appointments as $appointment) {
            try {
                $previousStatus = $appointment->status;

                // Actualizar directamente sin disparar events ni scopes
                DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->update([
                        'status' => 'noshow',
                        'updated_at' => now(),
                    ]);

                // Registrar el cambio de estado manualmente
                AppointmentStatus::create([
                    'appointment_id' => $appointment->id,
                    'previous_status' => $previousStatus,
                    'status' => 'noshow',
                    'user_id' => 1, // ID del administrador del sistema
                ]);

                $updated++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Error al marcar appointment ID {$appointment->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Se actualizaron {$updated} de {$count} citas a estado 'noshow'.");

        return 0;
    }
}
