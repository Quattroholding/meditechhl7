<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncounterSpecialityQuestionAnswer extends Model
{
    //
    protected $table = 'encounter_speciality_question_answers';

    protected $fillable = [
        'encounter_id', 'appointment_id', 'medical_speciality_id', 'answer', 'created_by', 'encounter_speciality_question_id',
    ];
}
