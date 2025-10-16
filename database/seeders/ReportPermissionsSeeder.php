<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para reporte de citas
        Permission::firstOrCreate(['name' => 'reports.appointments.view']);
        Permission::firstOrCreate(['name' => 'reports.appointments.excel']);
        Permission::firstOrCreate(['name' => 'reports.appointments.pdf']);

        // Asignar a roles
        $roles = ['admin', 'doctor', 'asistente'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo([
                    'reports.appointments.view',
                    'reports.appointments.excel',
                    'reports.appointments.pdf',
                ]);
            }
        }

        echo "✅ Permisos de reportes creados y asignados\n";
    }

}
