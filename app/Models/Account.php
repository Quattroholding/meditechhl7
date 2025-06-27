<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ScopedBy([ClientScope::class])]
class Account extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'identifier',
        'status',
        'type',
        'name',
        'description',
        'currency',
        'balance',
        'credit_limit',
        'available_balance',
        'patient_id',
        'organization_id',
        'coverage_id',
        'parent_account_id',
        'guarantor',
        'service_period_start',
        'service_period_end',
        'billing_status',
        'procedure_sequence',
        'external_id',
        'contact_details',
        'notes',
        'client_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'identifier' => 'array',
        'type' => 'array',
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'guarantor' => 'array',
        'service_period_start' => 'date',
        'service_period_end' => 'date',
        'billing_status' => 'array',
        'procedure_sequence' => 'array',
        'contact_details' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fhir_id)) {
                $model->fhir_id = 'account-'.Str::uuid();
            }
            if (empty($model->client_id)) {
                $model->client_id = auth()->user()?->getCurrentClient()?->id;
            }
        });

        static::saving(function ($model) {
            $model->calculateAvailableBalance();
        });
    }

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'organization_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function chargeItems(): HasMany
    {
        return $this->hasMany(ChargeItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeWithinServicePeriod($query, $date = null)
    {
        $date = $date ?? now();

        return $query->where(function ($q) use ($date) {
            $q->whereNull('service_period_start')
                ->orWhere('service_period_start', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('service_period_end')
                ->orWhere('service_period_end', '>=', $date);
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('balance', '>', 0)
            ->where('service_period_end', '<', now());
    }

    // Accessors & Mutators
    public function getAccountTypeAttribute(): string
    {
        if (is_array($this->type) && isset($this->type['coding'])) {
            foreach ($this->type['coding'] as $coding) {
                if (isset($coding['code'])) {
                    return $coding['code'];
                }
            }
        }

        return 'patient';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getAvailableCreditAttribute(): float
    {
        if (! $this->credit_limit) {
            return 0.0;
        }

        return (float) $this->credit_limit - (float) $this->balance;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->balance > 0 &&
               $this->service_period_end &&
               $this->service_period_end->isPast();
    }

    // Methods
    public function calculateAvailableBalance(): void
    {
        if ($this->credit_limit) {
            $this->available_balance = $this->credit_limit - $this->balance;
        } else {
            $this->available_balance = null;
        }
    }

    public function addCharge(float $amount, ?string $description = null): bool
    {
        if ($this->credit_limit && ($this->balance + $amount) > $this->credit_limit) {
            return false; // Exceeds credit limit
        }

        $this->balance += $amount;
        $this->calculateAvailableBalance();

        return $this->save();
    }

    public function addPayment(float $amount, ?string $description = null): bool
    {
        if ($amount > $this->balance) {
            return false; // Payment exceeds balance
        }

        $this->balance -= $amount;
        $this->calculateAvailableBalance();

        return $this->save();
    }

    public function createSubAccount(array $data): Account
    {
        return static::create(array_merge($data, [
            'parent_account_id' => $this->id,
            'client_id' => $this->client_id,
            'organization_id' => $this->organization_id,
            'currency' => $this->currency,
        ]));
    }

    public function suspend(?string $reason = null): bool
    {
        $this->status = 'on-hold';

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes."\n\n" : '')
                         .'Suspended: '.$reason.' ('.now()->toDateString().')';
        }

        return $this->save();
    }

    public function reactivate(): bool
    {
        $this->status = 'active';
        $this->notes = ($this->notes ? $this->notes."\n\n" : '')
                     .'Reactivated: '.now()->toDateString();

        return $this->save();
    }

    public function getTotalCharges(): float
    {
        return $this->chargeItems()->sum('total_price');
    }

    public function getTotalInvoiced(): float
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getTotalPaid(): float
    {
        return $this->invoices()->sum('amount_paid');
    }

    public function getOutstandingBalance(): float
    {
        return $this->getTotalInvoiced() - $this->getTotalPaid();
    }

    public static function createPatientAccount(Patient $patient, array $options = []): Account
    {
        return static::create(array_merge([
            'name' => "Patient Account - {$patient->full_name}",
            'description' => "Primary account for patient {$patient->full_name}",
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/account-type',
                        'code' => 'patient',
                        'display' => 'Patient Account',
                    ],
                ],
            ],
            'status' => 'active',
            'patient_id' => $patient->id,
            'client_id' => $patient->client_id,
        ], $options));
    }

    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'Account',
            'id' => $this->fhir_id,
            'identifier' => $this->identifier,
            'status' => $this->status,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'subject' => $this->patient ? [
                [
                    'reference' => "Patient/{$this->patient->fhir_id}",
                ],
            ] : null,
            'servicePeriod' => ($this->service_period_start || $this->service_period_end) ? [
                'start' => $this->service_period_start?->toISOString(),
                'end' => $this->service_period_end?->toISOString(),
            ] : null,
            'coverage' => $this->coverage_id ? [
                [
                    'coverage' => [
                        'reference' => "Coverage/{$this->coverage_id}",
                    ],
                ],
            ] : null,
            'guarantor' => $this->guarantor,
            'partOf' => $this->parent_account_id ? [
                'reference' => "Account/{$this->parentAccount->fhir_id}",
            ] : null,
        ];
    }
}
