<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'max_users',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_users' => 'integer'
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
