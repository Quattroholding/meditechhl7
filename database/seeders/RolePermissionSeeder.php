<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions with descriptions
        $permissions = [

            // Role and Permission management
            ['name' => 'manage-roles', 'description' => 'Gestionar roles de usuario', 'module' => 'roles'],
            ['name' => 'manage-permissions', 'description' => 'Gestionar permisos del sistema', 'module' => 'roles'],
            ['name' => 'manage-packages', 'description' => 'Gestionar los paquetes de licenciamiento de los clientes del sistema', 'module' => 'paquetes'],

            // User management
            ['name' => 'users.view', 'description' => 'Ver lista de usuarios del sistema', 'module' => 'usuarios'],
            ['name' => 'users.create', 'description' => 'Crear nuevos usuarios', 'module' => 'usuarios'],
            ['name' => 'users.edit', 'description' => 'Editar información de usuarios', 'module' => 'usuarios'],
            ['name' => 'users.delete', 'description' => 'Eliminar usuarios del sistema', 'module' => 'usuarios'],
            ['name' => 'users.profile', 'description' => 'Ver perfil del usuario', 'module' => 'usuarios'],
            ['name' => 'users.change_client', 'description' => 'Cambiar de cliente', 'module' => 'usuarios'],
            ['name' => 'users.activate', 'description' => 'Activar usuarios', 'module' => 'usuarios'],

            // Client management
            ['name' => 'clients.view', 'description' => 'Ver lista de clientes/organizaciones', 'module' => 'clientes'],
            ['name' => 'clients.create', 'description' => 'Registrar nuevos clientes', 'module' => 'clientes'],
            ['name' => 'clients.edit', 'description' => 'Editar información de clientes', 'module' => 'clientes'],
            ['name' => 'clients.delete', 'description' => 'Eliminar clientes del sistema', 'module' => 'clientes'],

            // Patient management
            ['name' => 'patients.view', 'description' => 'Ver lista y perfiles de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.create', 'description' => 'Registrar nuevos pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.edit', 'description' => 'Editar información de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.update', 'description' => 'Actualizar información de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.delete', 'description' => 'Eliminar registros de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.profile', 'description' => 'Ver perfiles de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.medical_history', 'description' => 'Ver historial medico de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.add_note', 'description' => 'Agregar nota general de pacientes', 'module' => 'pacientes'],
            ['name' => 'patients.insurance', 'description' => 'Gerstionar seguros de pacientes', 'module' => 'pacientes'],

            // Practitioner management
            ['name' => 'practitioners.view', 'description' => 'Ver lista de médicos y profesionales', 'module' => 'medicos'],
            ['name' => 'practitioners.create', 'description' => 'Registrar nuevos médicos', 'module' => 'medicos'],
            ['name' => 'practitioners.edit', 'description' => 'Editar información de médicos', 'module' => 'medicos'],
            ['name' => 'practitioners.update', 'description' => 'Actualizar información de médicos', 'module' => 'medicos'],
            ['name' => 'practitioners.delete', 'description' => 'Eliminar registros de médicos', 'module' => 'medicos'],
            ['name' => 'practitioners.profile', 'description' => 'Ver perfil de médicos', 'module' => 'medicos'],
            ['name' => 'practitioners.directory', 'description' => 'Ver directorio medico', 'module' => 'medicos'],
            ['name' => 'practitioners.add_assistant', 'description' => 'Agregar un nuevo asistente al Sistema', 'module' => 'medicos'],

            // Appointment management
            ['name' => 'appointments.view', 'description' => 'Ver calendario y lista de citas', 'module' => 'citas'],
            ['name' => 'appointments.create', 'description' => 'Agendar nuevas citas médicas', 'module' => 'citas'],
            ['name' => 'appointments.edit', 'description' => 'Modificar citas existentes', 'module' => 'citas'],
            ['name' => 'appointments.delete', 'description' => 'Cancelar o eliminar citas', 'module' => 'citas'],
            ['name' => 'appointments.calendar', 'description' => 'Ver calendario de citas', 'module' => 'citas'],

            // Consultation management
            ['name' => 'consultations.view', 'description' => 'Ver historial de consultas', 'module' => 'consultas'],
            ['name' => 'consultations.create', 'description' => 'Crear nuevas consultas médicas', 'module' => 'consultas'],
            ['name' => 'consultations.edit', 'description' => 'Editar consultas existentes', 'module' => 'consultas'],
            ['name' => 'consultations.delete', 'description' => 'Eliminar registros de consultas', 'module' => 'consultas'],

            // Branches management
            ['name' => 'branches.view', 'description' => 'Ver lista de sucursales', 'module' => 'sucursales'],
            ['name' => 'branches.create', 'description' => 'Crear nueva sucursal', 'module' => 'sucursales'],
            ['name' => 'branches.edit', 'description' => 'Editar sucursal', 'module' => 'sucursales'],
            ['name' => 'branches.delete', 'description' => 'Eliminar sucursal', 'module' => 'sucursales'],

            // Rooms management
            ['name' => 'rooms.view', 'description' => 'Ver lista de consultorios', 'module' => 'consultorios'],
            ['name' => 'rooms.create', 'description' => 'Crear nuevo consultorio', 'module' => 'consultorios'],
            ['name' => 'rooms.edit', 'description' => 'Editar consultorio', 'module' => 'consultorios'],
            ['name' => 'rooms.delete', 'description' => 'Eliminar consultorio', 'module' => 'consultorios'],

            // Medicine management
            ['name' => 'medicines.view', 'description' => 'Ver catálogo de medicamentos', 'module' => 'medicamentos'],
            ['name' => 'medicines.create', 'description' => 'Agregar nuevos medicamentos', 'module' => 'medicamentos'],
            ['name' => 'medicines.edit', 'description' => 'Editar información de medicamentos', 'module' => 'medicamentos'],
            ['name' => 'medicines.delete', 'description' => 'Eliminar medicamentos del catálogo', 'module' => 'medicamentos'],

            // Invoice management
            ['name' => 'invoices.view', 'description' => 'Ver facturas y estados de cuenta', 'module' => 'facturas'],
            ['name' => 'invoices.create', 'description' => 'Generar nuevas facturas', 'module' => 'facturas'],
            ['name' => 'invoices.edit', 'description' => 'Modificar facturas existentes', 'module' => 'facturas'],
            ['name' => 'invoices.delete', 'description' => 'Eliminar facturas', 'module' => 'facturas'],

            // Payment management
            ['name' => 'payments.view', 'description' => 'Ver historial de pagos', 'module' => 'pagos'],
            ['name' => 'payments.create', 'description' => 'Registrar nuevos pagos', 'module' => 'pagos'],
            ['name' => 'payments.edit', 'description' => 'Modificar registros de pagos', 'module' => 'pagos'],
            ['name' => 'payments.delete', 'description' => 'Eliminar registros de pagos', 'module' => 'pagos'],

            // Survey management
            ['name' => 'surveys.view', 'description' => 'Ver lista de encuestas', 'module' => 'encuestas'],
            ['name' => 'surveys.create', 'description' => 'Crear nueva encuesta', 'module' => 'encuestas'],
            ['name' => 'surveys.edit', 'description' => 'Modificar encuesta', 'module' => 'encuestas'],
            ['name' => 'surveys.delete', 'description' => 'Eliminar encuesta', 'module' => 'encuestas'],

            // Insurance Companies management
            ['name' => 'manage insurances', 'description' => 'Gestionar compañías de seguros médicos', 'module' => 'aseguradoras'],

            // Settings management
            ['name' => 'settings.create_user_procedures', 'description' => 'Configurar servicios de medicos', 'module' => 'configuracones'],
            ['name' => 'settings.create_consultation_template', 'description' => 'Configurar plantilla de consultas', 'module' => 'configuracones'],
            ['name' => 'settings.create_rapid_access', 'description' => 'Configurar lista de acceso rapido', 'module' => 'configuracones'],
            ['name' => 'settings.create_working_hour_user', 'description' => 'Configurar horario laboral', 'module' => 'configuracones'],
            ['name' => 'settings.signature_and_seal', 'description' => 'Configurar firma y sello digital de medico', 'module' => 'configuracones'],

            // Service Request
            ['name' => 'service_request.view', 'description' => 'Ver lista de Solicitud de Examenes', 'module' => 'examenes'],
            ['name' => 'service_request.upload_result', 'description' => 'Subir Resultado', 'module' => 'examenes'],
            // Dashboard access
            ['name' => 'dashboard.admin', 'description' => 'Acceso al dashboard de administrador', 'module' => 'dashboards'],
            ['name' => 'dashboard.doctor', 'description' => 'Acceso al dashboard de médico', 'module' => 'dashboards'],
            ['name' => 'dashboard.patient', 'description' => 'Acceso al dashboard de paciente', 'module' => 'dashboards'],
            ['name' => 'dashboard.client', 'description' => 'Acceso al dashboard de cliente', 'module' => 'dashboards'],
            ['name' => 'dashboard.assistence', 'description' => 'Acceso al dashboard de asistencia', 'module' => 'dashboards'],

            // Dashboard access
            ['name' => 'reports.appointments.view', 'description' => 'Ver reportes de citas', 'module' => 'reportes'],
            ['name' => 'reports.appointments.excel', 'description' => 'Descargar reportes de citas en excel', 'module' => 'reportes'],
            ['name' => 'reports.appointments.pdf', 'description' => 'Descargar reportes de citas en pdf', 'module' => 'reportes'],
            ['name' => 'reports.invoices-payments.view', 'description' => 'Ver reportes de facturas', 'module' => 'reportes'],
            ['name' => 'reports.invoices-payments.excel', 'description' => 'Descargar reportes de facturas en excel', 'module' => 'reportes'],
            ['name' => 'reports.invoices-payments.pdf', 'description' => 'Descargar reportes de facturas en pdf', 'module' => 'reportes'],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::whereName($permissionData['name'])->first();
            if ($permission) {
                $permission->description = $permissionData['description'];
                $permission->module = $permissionData['module'];
                $permission->save();
            } else {
                $permission = new Permission();
                $permission->name = $permissionData['name'];
                $permission->description = $permissionData['description'];
                $permission->module = $permissionData['module'];
                $permission->save();
            }

        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->givePermissionTo([
            'dashboard.doctor',
            'users.view',
            'users.create',
            'users.change_client',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.calendar',
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.update',
            'patients.medical_history',
            'patients.add_note',
            'patients.insurance',
            'consultations.view',
            'consultations.create',
            'consultations.edit',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'settings.create_user_procedures',
            'settings.create_consultation_template',
            'settings.create_rapid_access',
            'settings.create_working_hour_user',
            'settings.signature_and_seal',
            'service_request.view',
            'service_request.upload_result',
            'branches.view',
            'branches.create',
            'branches.edit',
            'branches.delete',
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',
            'medicines.view',
            'medicines.create',
            'practitioners.profile',
            'practitioners.directory',
            'practitioners.add_assistant',
            'practitioners.edit',
            'practitioners.update',
        ]);


        $assistantRole = Role::firstOrCreate(['name' => 'recepcionista']);
        $assistantRole->givePermissionTo([
            'dashboard.assistence',
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.add_note',
            'patients.insurance',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'appointments.calendar',
            'consultations.view',
            'practitioners.view',
            'practitioners.directory',
            'invoices.view',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'service_request.view',
            'service_request.upload_result',
            'users.profile',
            'users.change_client',
        ]);

        $patientRole = Role::firstOrCreate(['name' => 'paciente']);
        $patientRole->givePermissionTo([
            'dashboard.patient',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'appointments.calendar',
            'consultations.view',
            'invoices.view',
            'practitioners.directory',
            'patients.medical_history',
            'patients.profile',
            'patients.edit',
            'patients.update',
        ]);


        $adminClientRole = Role::firstOrCreate(['name' => 'admin client']);
        $adminClientRole->givePermissionTo([
            'dashboard.client',
            'users.view',
            'users.create',
            'users.delete',
            'users.activate',
            'practitioners.view',
            'practitioners.create',
            'practitioners.edit',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'consultations.view',
            'branches.view',
            'branches.create',
            'branches.edit',
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'medicines.view',
            'medicines.create',
            'medicines.edit',
            'invoices.view',
            'payments.view',
            'practitioners.directory',
            'surveys.view',
            'users.profile',
            'practitioners.add_assistant',
        ]);

        $doctorRole = Role::firstOrCreate(['name' => 'asistente medico']);
        $doctorRole->givePermissionTo([
            'dashboard.doctor',
            'users.view',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.calendar',
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.update',
            'patients.medical_history',
            'patients.add_note',
            'patients.insurance',
            'consultations.view',
            'consultations.create',
            'consultations.edit',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'settings.create_user_procedures',
            'settings.create_working_hour_user',
            'service_request.view',
            'service_request.upload_result',
            'branches.view',
            'branches.create',
            'branches.edit',
            'branches.delete',
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',
            'medicines.view',
            'practitioners.profile',
            'practitioners.directory',
            'practitioners.add_assistant',
            'practitioners.edit',
            'practitioners.update',
        ]);
    }
}
