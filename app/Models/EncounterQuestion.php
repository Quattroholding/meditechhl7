<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncounterQuestion extends Model
{
    //
    protected $table = 'encounter_speciality_questions';

    protected $fillable = [
        'medical_speciality_id',
        'question_esp',
        'question_eng',
        'options_esp',
        'options_eng',
        'description_esp',
        'description_eng',
    ];
}
