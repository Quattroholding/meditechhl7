<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd10Code extends Model
{
    protected $fillable = ['code', 'icd10_code', 'active', 'description', 'description_es'];

    public function getFullNameAttribute()
    {
        $display = app()->getLocale() === 'es' ? $this->description_es : $this->description;

        return $this->code.' | '.$display;
    }
}
