<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Seeder;

class PediatricEncounterSectionsSeeder extends Seeder
{
    /**
     * Create encounter sections for Pediatrics specialty (id=25).
     */
    public function run(): void
    {
        $sections = [
            [
                'name' => 'Vaccination Record',
                'name_esp' => 'Registro de Vacunación',
                'livewire_component_name' => 'consultation.vaccination-record',
                'category' => 'Pediatría',
                'medical_speciality_id' => 25,
                'obligatory' => false,
                'available_for_medical_assistant' => true,
                'order' => 6,
            ],
            [
                'name' => 'Growth Monitoring',
                'name_esp' => 'Monitoreo de Crecimiento',
                'livewire_component_name' => 'consultation.growth-tracking',
                'category' => 'Pediatría',
                'medical_speciality_id' => 25,
                'obligatory' => false,
                'available_for_medical_assistant' => true,
                'order' => 6,
            ],
        ];

        foreach ($sections as $section) {
            EncounterSection::updateOrCreate(
                [
                    'livewire_component_name' => $section['livewire_component_name'],
                    'medical_speciality_id' => $section['medical_speciality_id'],
                ],
                $section
            );
        }

        $this->command->info('Pediatric encounter sections created successfully.');
    }
}
