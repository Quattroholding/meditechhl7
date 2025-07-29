<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'description' => 'Paquete básico para clínicas pequeñas con funcionalidades esenciales',
            'max_users' => 5,
            'is_active' => true
        ]);

        Package::create([
            'name' => 'Estándar',
            'description' => 'Paquete estándar para clínicas medianas con funcionalidades completas',
            'max_users' => 15,
            'is_active' => true
        ]);

        Package::create([
            'name' => 'Premium',
            'description' => 'Paquete premium para hospitales y clínicas grandes con todas las funcionalidades',
            'max_users' => 50,
            'is_active' => true
        ]);

        Package::create([
            'name' => 'Empresarial',
            'description' => 'Paquete empresarial para grandes organizaciones de salud con usuarios ilimitados',
            'max_users' => 999,
            'is_active' => true
        ]);
    }
}
