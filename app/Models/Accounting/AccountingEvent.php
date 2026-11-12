<?php

namespace App\Models\Accounting;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingEvent extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'module',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function eventConfigs(): HasMany
    {
        return $this->hasMany(AccountingEventConfig::class);
    }
}
