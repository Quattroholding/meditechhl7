<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insurance extends BaseModel
{
    use HasFactory;

    protected $table = 'insurance_companies';

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

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'coverage_types' => 'array',
            'default_coverage_percentage' => 'decimal:2',
            'default_copay_amount' => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_insurances')
            ->withPivot('policy_number', 'group_number', 'subscriber_id', 'relationship', 'effective_date', 'expiry_date')
            ->withTimestamps();
    }
}
