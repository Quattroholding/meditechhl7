<?php

namespace App\Models;

use App\Models\Scopes\PatientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientInsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'insurance_company_id',
        'policy_number',
        'group_number',
        'subscriber_id',
        'subscriber_name',
        'relationship_to_subscriber',
        'effective_date',
        'expiration_date',
        'priority',
        'coverage_percentage',
        'copay_amount',
        'deductible_amount',
        'deductible_remaining',
        'out_of_pocket_max',
        'out_of_pocket_remaining',
        'is_active',
        'coverage_details',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'coverage_percentage' => 'decimal:2',
        'copay_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'deductible_remaining' => 'decimal:2',
        'out_of_pocket_max' => 'decimal:2',
        'out_of_pocket_remaining' => 'decimal:2',
        'is_active' => 'boolean',
        'coverage_details' => 'array',
    ];



    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function invoicesAsPrimary(): HasMany
    {
        return $this->hasMany(Invoice::class, 'primary_insurance_id');
    }

    public function invoicesAsSecondary(): HasMany
    {
        return $this->hasMany(Invoice::class, 'secondary_insurance_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('priority', 'primary');
    }

    public function scopeSecondary($query)
    {
        return $query->where('priority', 'secondary');
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
