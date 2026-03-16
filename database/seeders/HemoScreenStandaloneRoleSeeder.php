<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HemoScreenStandaloneRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create hemoscreen role with minimal permissions
        $hemoscreenRole = \Spatie\Permission\Models\Role::create([
            'name' => 'hemoscreen',
            'guard_name' => 'web',
        ]);

        // Minimal permissions for viewing HemoScreen results only
        $permissions = [
            'view hemoscreen results',
            'export hemoscreen results',
        ];

        foreach ($permissions as $permissionName) {
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            $hemoscreenRole->givePermissionTo($permission);
        }

        $this->command->info('HemoScreen standalone role created successfully with permissions.');
    }
}
