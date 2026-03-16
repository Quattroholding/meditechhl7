<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = [
        'practitioner_id',
        'name',
        'token',
        'allowed_ips',
        'scopes',
        'mode',
        'last_used_at',
        'last_used_ip',
        'expires_at',
        'active',
        'created_by',
        'description',
    ];

    protected $attributes = [
        'scopes' => '["*"]',
        'allowed_ips' => '["*"]',
        'mode' => 'integrated',
    ];

    protected function casts(): array
    {
        return [
            'allowed_ips' => 'array',
            'scopes' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    /**
     * Generate a new API token
     */
    public static function generateToken(): string
    {
        return 'mdt_'.Str::random(60);
    }

    /**
     * Check if the token is valid for the given IP
     */
    public function isValidForIp(string $ip): bool
    {
        // Check if token is active
        if (! $this->active) {
            return false;
        }

        // Check if token is expired
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check if IP is allowed (* means all IPs are allowed)
        if (in_array('*', $this->allowed_ips)) {
            return true;
        }

        // Check exact IP match
        if (in_array($ip, $this->allowed_ips)) {
            return true;
        }

        // Check CIDR ranges
        foreach ($this->allowed_ips as $allowedIp) {
            if ($this->isIpInRange($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    private function isIpInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return false;
        }

        [$subnet, $bits] = explode('/', $range);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);

            return ($ip & $mask) === ($subnet & $mask);
        }

        return false;
    }

    /**
     * Check if token has specific scope
     */
    public function hasScope(string $scope): bool
    {
        return in_array('*', $this->scopes) || in_array($scope, $this->scopes);
    }

    /**
     * Update last used information
     */
    public function updateLastUsed(string $ip): void
    {
        $this->update([
            'last_used_at' => now(),
            'last_used_ip' => $ip,
        ]);
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Check if token is expired
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expires_at && $this->expires_at->isPast()
        );
    }

    /**
     * Get token status
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->active) {
                    return 'inactive';
                }

                if ($this->expires_at && $this->expires_at->isPast()) {
                    return 'expired';
                }

                return 'active';
            }
        );
    }

    /**
     * Get the practitioner that owns the token
     */
    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    /**
     * Get localhost API token
     */
    public static function local(): ?self
    {
        return static::where('name', 'localhost')->first();
    }

    /**
     * Find an active token by token string
     */
    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)->active()->first();
    }

    /**
     * Check if token is in standalone mode
     */
    public function isStandaloneMode(): bool
    {
        return $this->mode === 'standalone';
    }

    /**
     * Check if token is in integrated mode
     */
    public function isIntegratedMode(): bool
    {
        return $this->mode === 'integrated';
    }

    /**
     * Scope to filter standalone tokens
     */
    public function scopeStandalone($query)
    {
        return $query->where('mode', 'standalone');
    }

    /**
     * Scope to filter integrated tokens
     */
    public function scopeIntegrated($query)
    {
        return $query->where('mode', 'integrated');
    }
}
