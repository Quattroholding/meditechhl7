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
            'list clients',
            'create clients',
            'edit clients',
            'delete clients',

            'list branches',
            'create branches',
            'edit branches',
            'delete branches',

            'list rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',

            'list patients',
            'create patients',
            'edit patients',
            'delete patients',
            'profile patients',
            'medical record patients',

            'list practitioners',
            'create practitioners',
            'edit practitioners',
            'delete practitioners',
            'profile practitioners',

            'list users',
            'create users',
            'edit users',
            'delete users',
            'profile users',

            'edit procedure settings',
            'edit template settings',
            'edit rapid access settings',
            'edit working hours settings',


            'list appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records',
            // Agrega más permisos según necesites

            'view medical directory'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission,'guard_name'=>'web']);
        }

        // Crear roles y asignar permisos
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $doctorRole = Role::create(['name' => 'doctor','guard_name'=>'web']);
        $doctorRole->givePermissionTo([
            'list branches',
            'create branches',
            'edit branches',
            'delete branches',
            'list rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',
            'list patients',
            'create patients',
            'edit patients',
            'delete patients',
            'profile patients',
            'medical record patients',
            'edit procedure settings',
            'edit template settings',
            'edit rapid access settings',
            'edit working hours settings',
            'list appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records',
            'view medical directory'
        ]);

        $asistenteRole = Role::create(['name' => 'asistente','guard_name'=>'web']);
        $asistenteRole->givePermissionTo([
            'list branches',
            'create branches',
            'edit branches',
            'delete branches',
            'list rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',
            'list patients',
            'create patients',
            'edit patients',
            'delete patients',
            'profile patients',
            'list appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records',
            'view medical directory'
        ]);

        $patientRole = Role::create(['name' => 'paciente','guard_name'=>'web']);
        $patientRole->givePermissionTo([
            'list appointments',
            'create appointments',
            'edit appointments',
            'cancel appointments',
            'view medical records',
            'view medical directory'
        ]);

    }
}
