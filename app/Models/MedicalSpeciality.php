<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalSpeciality  extends Model
{
    protected $table='medical_specialties';
    protected $fillable = ['code', 'name','is_surgical'];
}
