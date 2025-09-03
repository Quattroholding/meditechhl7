<?php

namespace App\Models;

class MedicineActiveComponent extends BaseModel
{
    protected $fillable = ['medicine_id', 'name', 'mgs'];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
