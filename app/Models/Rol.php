<?php

namespace App\Models;

use App\Models\Scopes\RolScope;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table='roles';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new RolScope());
    }
}
