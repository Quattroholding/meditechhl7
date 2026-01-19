<?php

namespace Database\Seeders;

use App\Models\SetupReminderConfig;
use Illuminate\Database\Seeder;

class SetupReminderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reminders = [
            [
                'reminder_key' => 'subscription',
                'title' => 'Verificación de Suscripción',
                'is_active' => true,
                'is_required' => true,
                'order' => 1,
                'description' => 'Verifica el estado de pago de la suscripción y límites de citas.',
            ],
            [
                'reminder_key' => 'branch',
                'title' => 'Crear Sucursal',
                'is_active' => true,
                'is_required' => true,
                'order' => 2,
                'description' => 'Crear al menos una sucursal para comenzar a usar el sistema.',
            ],
            [
                'reminder_key' => 'consulting_room',
                'title' => 'Crear Consultorio',
                'is_active' => true,
                'is_required' => true,
                'order' => 3,
                'description' => 'Configurar al menos un consultorio para poder agendar citas.',
            ],
            [
                'reminder_key' => 'patient',
                'title' => 'Registrar Pacientes',
                'is_active' => true,
                'is_required' => true,
                'order' => 4,
                'description' => 'Registrar al menos un paciente para comenzar a agendar citas.',
            ],
            [
                'reminder_key' => 'service_catalog',
                'title' => 'Catálogo de Servicios',
                'is_active' => true,
                'is_required' => false,
                'order' => 5,
                'description' => 'Configurar los servicios médicos que ofreces con sus precios.',
            ],
            [
                'reminder_key' => 'working_hours',
                'title' => 'Horarios de Atención',
                'is_active' => true,
                'is_required' => false,
                'order' => 6,
                'description' => 'Definir tus horarios de trabajo para que los pacientes sepan cuándo estás disponible.',
            ],
            [
                'reminder_key' => 'rapid_access',
                'title' => 'Accesos Rápidos',
                'is_active' => false, // ⚠️ Este está desactivado por defecto
                'is_required' => false,
                'order' => 8,
                'description' => 'Configurar accesos rápidos de laboratorios, imágenes y procedimientos.',
            ],
            [
                'reminder_key' => 'signature_and_seal',
                'title' => 'Firma y Sello Médico',
                'is_active' => true, // ⚠️ Este está desactivado por defecto
                'is_required' => false,
                'order' => 7,
                'description' => 'Configurar firma y sello médico para enviar recetas automaticas a pacientes.',
            ],
        ];

        foreach ($reminders as $reminder) {
            SetupReminderConfig::updateOrCreate(
                ['reminder_key' => $reminder['reminder_key']],
                $reminder
            );
        }
    }
}
