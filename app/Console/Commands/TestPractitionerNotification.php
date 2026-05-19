<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentBookedForPractitionerNotification;
use Illuminate\Console\Command;

class TestPractitionerNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:practitioner-notification {appointment_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email de prueba de notificación de cita al médico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');

        // Si no se proporciona ID, buscar la última cita con estado 'booked'
        if (! $appointmentId) {
            $appointment = Appointment::where('status', 'booked')
                ->with(['patient', 'practitioner', 'consultingRoom.branch', 'medicalSpeciality', 'client'])
                ->latest()
                ->first();

            if (! $appointment) {
                $this->error('No se encontró ninguna cita con estado "booked".');
                $this->info('Por favor proporciona un ID de cita: php artisan test:practitioner-notification {id}');

                return 1;
            }

            $this->info("Usando cita #{$appointment->id}");
        } else {
            $appointment = Appointment::with(['patient', 'practitioner', 'consultingRoom.branch', 'medicalSpeciality', 'client'])
                ->find($appointmentId);

            if (! $appointment) {
                $this->error("No se encontró la cita con ID #{$appointmentId}");

                return 1;
            }
        }

        // Mostrar detalles de la cita
        $this->info('═══════════════════════════════════════════════════');
        $this->info('Detalles de la Cita:');
        $this->info('═══════════════════════════════════════════════════');
        $this->line("ID: {$appointment->id}");
        $this->line("Paciente: {$appointment->patient->name}");
        $this->line("Médico: {$appointment->practitioner->name}");
        $this->line("Fecha: {$appointment->start->format('d/m/Y H:i')}");
        $this->line("Estado: {$appointment->status->value}");
        $this->line('═══════════════════════════════════════════════════');

        // Obtener email de prueba desde .env
        $testEmail = config('mail.testing_practitioner_email') ?? config('mail.testing_patient_email');

        if (! $testEmail) {
            $this->error('No se encontró email de prueba en .env');
            $this->info('Configura MAIL_TESTING_PRACTITIONER_EMAIL o MAIL_TESTING_PATIENT_EMAIL');

            return 1;
        }

        $this->info("\nEnviando notificación a: {$testEmail}");
        $this->info('Por favor espera...');

        try {
            // Guardar datos originales del practitioner
            $originalEmail = $appointment->practitioner->email;
            $originalWhatsApp = $appointment->practitioner->whatsapp_phone;
            $originalPhone = $appointment->practitioner->phone;

            // Temporalmente cambiar al email de prueba y desactivar WhatsApp
            $appointment->practitioner->email = $testEmail;
            $appointment->practitioner->whatsapp_phone = null; // Forzar uso de email
            $appointment->practitioner->phone = null; // Forzar uso de email

            // Enviar notificación al practitioner (con email de prueba)
            $appointment->practitioner->notify(new AppointmentBookedForPractitionerNotification($appointment));

            // Restaurar datos originales
            $appointment->practitioner->email = $originalEmail;
            $appointment->practitioner->whatsapp_phone = $originalWhatsApp;
            $appointment->practitioner->phone = $originalPhone;

            $this->newLine();
            $this->info('✅ Notificación enviada exitosamente!');
            $this->info("📧 Revisa el correo: {$testEmail}");
            $this->newLine();

            // Mostrar información adicional
            $this->comment('Detalles del email:');
            $this->line("  • Asunto: Nueva Cita Médica Agendada - {$appointment->client->name}");
            $this->line('  • Vista: emails.appointment-booked-practitioner');
            $this->line('  • Incluye: Botones de Confirmar y Cancelar');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al enviar la notificación:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->line($e->getTraceAsString());

            return 1;
        }
    }
}
