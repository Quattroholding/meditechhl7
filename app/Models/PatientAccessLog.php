<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAccessLog extends Model
{
    protected $table = 'patient_access_logs';

    protected $fillable = [
        'user_id',
        'patient_id',
        'client_id',
        'action_type',
        'resource_type',
        'ip_address',
        'user_agent',
        'session_id',
        'metadata',
        'access_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'access_timestamp' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes for common queries
    public function scopeViews($query)
    {
        return $query->where('action_type', 'view');
    }

    public function scopeDownloads($query)
    {
        return $query->where('action_type', 'download');
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('access_timestamp', [$fromDate, $toDate]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
