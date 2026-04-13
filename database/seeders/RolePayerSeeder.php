<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $rolesPorPaquete = [
            'Básico' => 'doctor', 
            'Estándar' => 'doctor',
            'Premium' => 'doctor, admin client',
            'Empresarial' => 'admin client',
        ];
        $packages = DB::table('packages')->get();

        foreach ($packages as $package) {
            $rolePayer = $rolesPorPaquete[$package->name] ?? null;

            DB::table('packages')
                ->where('name', $package->name)
                ->update(['role_payer' => $rolePayer]);
        }
    }
}
