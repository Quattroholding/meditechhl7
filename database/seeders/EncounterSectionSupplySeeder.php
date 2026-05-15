<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Seeder;

class EncounterSectionSupplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if section already exists
        $exists = EncounterSection::where('name', 'Supplies')->exists();

        if ($exists) {
            $this->command->info('Supplies section already exists.');

            return;
        }

        // Create the supplies section
        EncounterSection::create([
            'name' => 'Supplies',
            'name_esp' => 'Suministros',
            'order' => 11.5, // After Medicamentos (11), before other sections
            'livewire_component_name' => 'consultation.supply-requests',
            'livewire_component_fields' => null,
            'category' => null,
            'medical_speciality_id' => null,
            'obligatory' => 0,
            'available_for_medical_assistant' => 0,
        ]);

        $this->command->info('Supplies section created successfully.');
    }
}
