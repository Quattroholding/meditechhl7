<?php

namespace App\Models;

class EncounterTemplate extends BaseModel
{
    protected $table = 'encounter_template';

    protected $fillable = ['type', 'client_id', 'user_id', 'encounter_section_id', 'encounter_section_fields'];

    protected function casts(): array
    {
        return [
            'encounter_section_fields' => 'array',
        ];
    }
}
