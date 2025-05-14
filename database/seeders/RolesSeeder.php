<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            'view appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records',
            // Agrega más permisos según necesites
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission,'guard_name'=>'web']);
        }

        // Crear roles y asignar permisos
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $doctorRole = Role::create(['name' => 'doctor','guard_name'=>'web']);
        $doctorRole->givePermissionTo([
            'view patients',
            'create patients',
            'edit patients',
            'view appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records'
        ]);

        $asistenteRole = Role::create(['name' => 'asistente','guard_name'=>'web']);
        $asistenteRole->givePermissionTo([
            'view patients',
            'create patients',
            'edit patients',
            'view appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
        ]);

        $patientRole = Role::create(['name' => 'paciente','guard_name'=>'web']);
        $patientRole->givePermissionTo([
            'view appointments',
            'view medical records'
        ]);

    }
}
