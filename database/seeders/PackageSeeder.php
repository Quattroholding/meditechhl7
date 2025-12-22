<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::create([
            'name' => 'Básico',
            'description' => 'Paquete básico para clínicas pequeñas y o consultorios con funcionalidades esenciales',
            'max_users' => 1,
            'max_doctors_included' => 1,
            'is_active' => true,
            'base_price' => 49.99,
        ]);

        Package::create([
            'name' => 'Estándar',
            'description' => 'Paquete estándar para clínicas medianas con funcionalidades completas',
            'max_users' => 1,
            'max_doctors_included' => 1,
            'is_active' => true,
            'base_price' => 74.99,
            'agent_available' => true,
        ]);

        Package::create([
            'name' => 'Premium',
            'description' => 'Paquete premium para hospitales y clínicas grandes con todas las funcionalidades',
            'max_users' => 4,
            'max_doctors_included' => 1,
            'is_active' => true,
            'base_price' => 124.99,
            'agent_available' => true,
        ]);

        Package::create([
            'name' => 'Empresarial',
            'description' => 'Paquete empresarial para grandes organizaciones de salud con usuarios ilimitados',
            'max_users' => 50,
            'max_doctors_included' => 20,
            'is_active' => true,
            'base_price' => 999.99,
        ]);
    }
}
