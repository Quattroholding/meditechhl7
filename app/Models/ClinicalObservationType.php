<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalObservationType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'system',
        'default_unit',
        'min_normal_value',
        'max_normal_value',
        'description',
        'procedure',
        'possible_abnormalities'
    ];

    protected $casts = [
        'possible_abnormalities' => 'array'
    ];

    public function scopeVitalSigns($query)
    {
        return $query->where('category', 'vital_sign');
    }

    public function scopePhysicalExams($query)
    {
        return $query->where('category', 'physical_exam');
    }
}
