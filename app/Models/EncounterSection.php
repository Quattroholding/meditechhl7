<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncounterSection extends BaseModel
{
    protected $fillable=['name','name_esp','table_list','table_list_filter','livewire_component_name','livewire_component_fields','category','medical_speciality_id'];

    protected $casts = [
        'livewire_component_fields' => 'array',
    ];
}
