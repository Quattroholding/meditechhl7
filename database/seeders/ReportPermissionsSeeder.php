<?php

namespace Database\Seeders;

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
        // Definir reportes y sus roles permitidos
        $reports = [
            'appointments' => ['admin', 'doctor', 'recepcionista'],
            'invoices-payments' => ['admin', 'doctor', 'recepcionista'],
        ];

        foreach ($reports as $reportName => $roles) {
            // Crear permisos para el reporte
            Permission::firstOrCreate(['name' => "reports.{$reportName}.view",'module'=>'reportes']);
            Permission::firstOrCreate(['name' => "reports.{$reportName}.excel",'module'=>'reportes']);
            Permission::firstOrCreate(['name' => "reports.{$reportName}.pdf",'module'=>'reportes']);

            // Asignar a roles correspondientes
            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $role->givePermissionTo([
                        "reports.{$reportName}.view",
                        "reports.{$reportName}.excel",
                        "reports.{$reportName}.pdf",
                    ]);
                }
            }

            echo "✅ Permisos creados para reporte: {$reportName}\n";
        }

        echo "\n✅ Todos los permisos de reportes creados y asignados correctamente\n";
    }
}
