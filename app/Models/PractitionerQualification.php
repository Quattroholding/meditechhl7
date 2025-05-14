<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PractitionerQualification extends Model
{
    protected $fillable=['code','system','display','period_start','period_end'];

    public function practitioner(){
        return $this->belongsTo(Practitioner::class);
    }
}
