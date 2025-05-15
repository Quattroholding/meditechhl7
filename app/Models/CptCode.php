<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class CptCode extends Model
{
    protected $fillable = ['code', 'description','description_es', 'category'];

    public function procedures(): HasMany { return $this->hasMany(Procedure::class); }

    public function area(){
        return $this->belongsTo(CptArea::class);
    }

    public function getFullNameAttribute() {
        return $this->code . ' | ' . $this->description_es;
    }
}
