<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RapidAccess extends BaseModel
{
    protected $table='rapid_access';
    protected $fillable=['type','client_id','user_id','cpt_id','active','encounter_section_id'];

    public function consultationField(){
        return $this->belongsTo(ConsultationField::class);
    }

    public function cpt(){
        return $this->belongsTo(CptCode::class);
    }
}
