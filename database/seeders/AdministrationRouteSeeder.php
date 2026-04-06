<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdministrationRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            ['name' => 'Intravenosa', 'abbreviation' => 'IV'],
            ['name' => 'Intramuscular', 'abbreviation' => 'IM'],
            ['name' => 'Subcutánea', 'abbreviation' => 'SC'],
            ['name' => 'Tópica', 'abbreviation' => 'Tópica'],
            ['name' => 'Inhalación', 'abbreviation' => 'INH'],
            ['name' => 'Sublingual', 'abbreviation' => 'SL'],
            ['name' => 'Rectal', 'abbreviation' => 'PR'],
            ['name' => 'Vaginal', 'abbreviation' => 'PV'],
            ['name' => 'Oftálmica', 'abbreviation' => 'OFT'],
            ['name' => 'Ótica', 'abbreviation' => 'OT'],
            ['name' => 'Nasal', 'abbreviation' => 'NAS'],
            ['name' => 'Transdérmica', 'abbreviation' => 'TD'],
            ['name' => 'Vía Oral', 'abbreviation' => 'VO'],
        ];

        foreach ($routes as $route) {
            \App\Models\AdministrationRoute::updateOrCreate($route);
        }
    }
}
