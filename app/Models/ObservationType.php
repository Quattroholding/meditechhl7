<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObservationType extends Model
{
    protected $fillable = ['code', 'system', 'display', 'category', 'unit'];

    public function observations(): HasMany { return $this->hasMany(Observation::class); }
}
