<?php

namespace App\Models;

use App\Models\Scopes\MedicineScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends BaseModel
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new MedicineScope);
    }

    protected $table = 'medicines';

    protected $fillable = [
        'ndc_code',
        'home_name',
        'generic_name',
        'type',
        'mgs',
        'mgs_type',
        'active',
        'narcotic',
        'client_id',
        'user_id',
        'source',
        'price',
        'product_type',
        'usage_indications',
        'porpuse',
        'indication_and_usage',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function components()
    {
        return $this->hasMany(MedicineActiveComponent::class);
    }

    public function getFullNameAttribute()
    {
        return $this->generic_name.' ('.$this->type.' '.$this->mgs.' '.$this->mgs_type.')';
    }

    public function getConcentrationAttribute()
    {
        return $this->mgs.' '.$this->mgs_type;
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }
}
