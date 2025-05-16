<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncounterTemplate extends BaseModel
{
    protected $table='encounter_template';
    protected $fillable=['type','client_id','user_id','encounter_section_id','encounter_section_fields'];
}
