<?php

namespace App\Models\Finance;

use App\Models\Accounting\JournalEntryLine;
use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\MedicalSpeciality;
use App\Models\Scopes\CostCenterScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCenter extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'client_id',
        'code',
        'name',
        'description',
        'branch_id',
        'medical_speciality_id',
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

    protected static function booted(): void
    {
        static::addGlobalScope(new CostCenterScope);
    }

    // Relaciones

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicalSpeciality(): BelongsTo
    {
        return $this->belongsTo(MedicalSpeciality::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function supplierInvoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function costDistributions(): HasMany
    {
        return $this->hasMany(CostDistribution::class);
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Métodos de negocio

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
