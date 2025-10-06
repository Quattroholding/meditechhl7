<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class PanamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $panama = Country::create([
            'name' => 'Panamá',
            'iso_code' => 'PAN',
        ]);

        $provinces = [
            ['name' => 'Bocas del Toro', 'capital' => 'Bocas del Toro'],
            ['name' => 'Coclé', 'capital' => 'Penonomé'],
            ['name' => 'Colón', 'capital' => 'Colón'],
            ['name' => 'Chiriquí', 'capital' => 'David'],
            ['name' => 'Darién', 'capital' => 'La Palma'],
            ['name' => 'Herrera', 'capital' => 'Chitré'],
            ['name' => 'Los Santos', 'capital' => 'Las Tablas'],
            ['name' => 'Panamá', 'capital' => 'Ciudad de Panamá'],
            ['name' => 'Panamá Oeste', 'capital' => 'La Chorrera'],
            ['name' => 'Veraguas', 'capital' => 'Santiago'],
        ];

        foreach ($provinces as $province) {
            State::create([
                'country_id' => $panama->id,
                'name' => $province['name'],
                'capital' => $province['capital'],
            ]);
        }
    }
}
