<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ServiceRequestResult extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'fhir_id',
        'service_request_id',
        'patient_id',
        'practitioner_id',
        'status',
        'result_type',
        'code',
        'code_system',
        'code_display',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'file_hash',
        'metadata',
        'result_date',
        'uploaded_at',
        'observations',
        'notes',
        'interpretation',
        'reference_range',
        'specimen_info',
        'version',
        'replaces_id',
        'effective_date',
        'issued_date',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'specimen_info' => 'array',
            'result_date' => 'datetime',
            'uploaded_at' => 'datetime',
            'effective_date' => 'datetime',
            'issued_date' => 'datetime',
            'file_size' => 'integer',
            'version' => 'integer',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function replacedResult(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestResult::class, 'replaces_id');
    }

    public function replacements()
    {
        return $this->hasMany(ServiceRequestResult::class, 'replaces_id');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    public function isPreliminary(): bool
    {
        return $this->status === 'preliminary';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function hasAbnormalInterpretation(): bool
    {
        return in_array($this->interpretation, ['critical', 'high', 'low', 'abnormal']);
    }

    public function generateFileHash(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    public function verifyFileIntegrity(): bool
    {
        if (!Storage::exists($this->file_path)) {
            return false;
        }

        $currentHash = hash_file('sha256', Storage::path($this->file_path));
        return $currentHash === $this->file_hash;
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByResultType($query, string $resultType)
    {
        return $query->where('result_type', $resultType);
    }

    public function scopeByPatient($query, int $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByServiceRequest($query, int $serviceRequestId)
    {
        return $query->where('service_request_id', $serviceRequestId);
    }

    public function scopeFinalResults($query)
    {
        return $query->where('status', 'final');
    }

    public function scopePreliminaryResults($query)
    {
        return $query->where('status', 'preliminary');
    }

    public function scopeAbnormalResults($query)
    {
        return $query->whereIn('interpretation', ['critical', 'high', 'low', 'abnormal']);
    }
}
