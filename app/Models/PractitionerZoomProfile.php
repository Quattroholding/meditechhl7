<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PractitionerZoomProfile extends Model
{
    protected $fillable = [
        'practitioner_id',
        'zoom_user_id',
        'zoom_email',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'verified_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relación con Practitioner
     */
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    /**
     * Verificar si el perfil de Zoom está configurado y verificado
     */
    public function isConfigured(): bool
    {
        return $this->verified_at !== null && $this->zoom_user_id !== null;
    }

    /**
     * Verificar si el token ha expirado
     */
    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return false;
        }

        return now()->isAfter($this->token_expires_at);
    }
}
