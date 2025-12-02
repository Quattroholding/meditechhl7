<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SkinLesion extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'body_structure_id',
        'patient_id',
        'encounter_id',
        'practitioner_id',
        'lesion_type',
        'color',
        'size_length_mm',
        'size_width_mm',
        'size_height_mm',
        'asymmetry',
        'border',
        'color_variation',
        'diameter',
        'evolving',
        'risk_level',
        'clinical_notes',
        'first_observed_date',
        'last_examination_date',
        'requires_biopsy',
        'requires_removal',
        'next_follow_up_date',
        'status',
        'treatment_plan',
    ];

    protected function casts(): array
    {
        return [
            'size_length_mm' => 'decimal:2',
            'size_width_mm' => 'decimal:2',
            'size_height_mm' => 'decimal:2',
            'requires_biopsy' => 'boolean',
            'requires_removal' => 'boolean',
            'first_observed_date' => 'date',
            'last_examination_date' => 'date',
            'next_follow_up_date' => 'date',
        ];
    }

    // Relationships

    public function bodyStructure(): BelongsTo
    {
        return $this->belongsTo(BodyStructure::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function marking(): HasOne
    {
        return $this->hasOne(BodySiteMarking::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'subject');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRiskLevel($query, string $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->where('next_follow_up_date', '>=', now())
            ->where('status', 'active');
    }

    public function scopeOverdueFollowUp($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->where('next_follow_up_date', '<', now())
            ->where('status', 'active');
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'suspicious']);
    }

    public function scopeByLesionType($query, string $type)
    {
        return $query->where('lesion_type', $type);
    }

    // Helper methods

    public function getAbcdeScoreAttribute(): int
    {
        $score = 0;

        if ($this->asymmetry === 'asymmetric') {
            $score++;
        }
        if ($this->border === 'irregular') {
            $score++;
        }
        if (! empty($this->color_variation)) {
            $score++;
        }
        if ($this->diameter === 'greater_than_6mm') {
            $score++;
        }
        if ($this->evolving === 'changing') {
            $score++;
        }

        return $score;
    }

    public function getSizeAreaAttribute(): ?float
    {
        if ($this->size_length_mm && $this->size_width_mm) {
            return round($this->size_length_mm * $this->size_width_mm, 2);
        }

        return null;
    }

    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'low' => '#22C55E',      // Green
            'moderate' => '#FBBF24', // Yellow
            'high' => '#F97316',     // Orange
            'suspicious' => '#EF4444', // Red
            default => '#6B7280',    // Gray
        };
    }

    public function isOverdue(): bool
    {
        return $this->next_follow_up_date && $this->next_follow_up_date->isPast();
    }

    public function getDaysUntilFollowUpAttribute(): ?int
    {
        if (! $this->next_follow_up_date) {
            return null;
        }

        return now()->diffInDays($this->next_follow_up_date, false);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'active' => 'success',
            'removed' => 'secondary',
            'resolved' => 'info',
            'under_treatment' => 'warning',
        ];

        $color = $colors[$this->status] ?? 'secondary';

        return "<span class='badge badge-{$color}'>{$this->status}</span>";
    }

    public function hasSignificantChange(?self $previousLesion): bool
    {
        if (! $previousLesion) {
            return false;
        }

        // Check for size increase > 20%
        if ($previousLesion->size_length_mm) {
            $sizeIncrease = (($this->size_length_mm - $previousLesion->size_length_mm) / $previousLesion->size_length_mm) * 100;
            if ($sizeIncrease > 20) {
                return true;
            }
        }

        // Check for risk level escalation
        $riskLevels = ['low' => 1, 'moderate' => 2, 'high' => 3, 'suspicious' => 4];
        if ($riskLevels[$this->risk_level] > $riskLevels[$previousLesion->risk_level]) {
            return true;
        }

        // Check for ABCDE criteria changes
        if ($this->asymmetry !== $previousLesion->asymmetry && $this->asymmetry === 'asymmetric') {
            return true;
        }
        if ($this->border !== $previousLesion->border && $this->border === 'irregular') {
            return true;
        }
        if ($this->evolving !== $previousLesion->evolving && $this->evolving === 'changing') {
            return true;
        }

        return false;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->first_observed_date)) {
                $model->first_observed_date = now();
            }
            if (empty($model->last_examination_date)) {
                $model->last_examination_date = now();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['size_length_mm', 'size_width_mm', 'risk_level', 'asymmetry', 'border', 'evolving'])) {
                $model->last_examination_date = now();
            }
        });
    }
}
