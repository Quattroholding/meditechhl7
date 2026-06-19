<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Seeder;

class FileUploadEncounterSectionSeeder extends Seeder
{
    public function run(): void
    {
        $maxOrder = EncounterSection::max('order') ?? 0;

        EncounterSection::updateOrCreate(
            [
                'livewire_component_name' => 'consultation.file-upload',
            ],
            [
                'name' => 'Consultation Files',
                'name_esp' => 'Archivos de Consulta',
                'order' => $maxOrder + 1,
                'table_list' => null,
                'table_list_filter' => null,
                'livewire_component_fields' => null,
                'category' => null,
                'medical_speciality_id' => null,
                'obligatory' => 0,
                'available_for_medical_assistant' => 0,
            ]
        );
    }
}
