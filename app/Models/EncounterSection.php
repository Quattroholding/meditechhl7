<?php

namespace App\Models;

class EncounterSection extends BaseModel
{
    protected $fillable = ['name', 'name_esp', 'icon', 'table_list', 'table_list_filter', 'livewire_component_name', 'livewire_component_fields', 'category', 'medical_speciality_id', 'obligatory', 'available_for_medical_assistant', 'order'];

    protected $casts = [
        'livewire_component_fields' => 'array',
    ];
}
