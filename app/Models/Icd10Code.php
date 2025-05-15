<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd10Code extends Model
{
    protected $fillable = ['code','icd10_code','active', 'description','description_es'];

    public function getFullNameAttribute() {
        return $this->code . ' | ' . $this->description_es;
    }
}
