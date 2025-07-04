<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceCompany extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'contact_person',
        'contact_email',
        'contact_phone',
        'default_coverage_percentage',
        'default_copay_amount',
        'is_active',
        'coverage_types',
        'notes',
    ];

    protected $casts = [
        'default_coverage_percentage' => 'decimal:2',
        'default_copay_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'coverage_types' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function patientPolicies(): HasMany
    {
        return $this->hasMany(PatientInsurancePolicy::class);
    }

    public function activePolicies(): HasMany
    {
        return $this->patientPolicies()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
