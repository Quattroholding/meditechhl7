<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends BaseModel
{
     protected $fillable=['user_id','practitioner_id','patient_id','encounter_id','note','active'];

    public function user(){
        return $this->belongsTo(User::class);
    }

     public function practitioner(){
         return $this->belongsTo(Practitioner::class);
     }

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    public function encounter(){
        return $this->belongsTo(Encounter::class);
    }
}
