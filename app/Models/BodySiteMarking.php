<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodySiteMarking extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'body_structure_id',
        'skin_lesion_id',
        'body_view',
        'position_x',
        'position_y',
        'marker_color',
        'marker_shape',
        'marker_size',
        'label',
        'notes',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'position_x' => 'decimal:4',
            'position_y' => 'decimal:4',
            'marker_size' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    // Relationships

    public function bodyStructure(): BelongsTo
    {
        return $this->belongsTo(BodyStructure::class);
    }

    public function skinLesion(): BelongsTo
    {
        return $this->belongsTo(SkinLesion::class);
    }

    // Scopes

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByView($query, string $view)
    {
        return $query->where('body_view', $view);
    }

    public function scopeAnterior($query)
    {
        return $query->where('body_view', 'anterior');
    }

    public function scopePosterior($query)
    {
        return $query->where('body_view', 'posterior');
    }

    public function scopeLeftLateral($query)
    {
        return $query->where('body_view', 'left_lateral');
    }

    public function scopeRightLateral($query)
    {
        return $query->where('body_view', 'right_lateral');
    }

    // Helper methods

    public function getCoordinatesAttribute(): array
    {
        return [
            'x' => (float) $this->position_x,
            'y' => (float) $this->position_y,
        ];
    }

    public function getStyleAttribute(): string
    {
        return "left: {$this->position_x}%; top: {$this->position_y}%; background-color: {$this->marker_color}; width: {$this->marker_size}px; height: {$this->marker_size}px;";
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'body_view' => $this->body_view,
            'x' => (float) $this->position_x,
            'y' => (float) $this->position_y,
            'color' => $this->marker_color,
            'shape' => $this->marker_shape,
            'size' => $this->marker_size,
            'label' => $this->label,
            'notes' => $this->notes,
            'is_visible' => $this->is_visible,
            'risk_level' => $this->skinLesion?->risk_level,
            'lesion_type' => $this->skinLesion?->lesion_type,
        ];
    }

    public function getViewDisplayNameAttribute(): string
    {
        return match ($this->body_view) {
            'anterior' => 'Vista Anterior',
            'posterior' => 'Vista Posterior',
            'left_lateral' => 'Vista Lateral Izquierda',
            'right_lateral' => 'Vista Lateral Derecha',
            default => ucfirst($this->body_view),
        };
    }

    public function getShapeIconAttribute(): string
    {
        return match ($this->marker_shape) {
            'circle' => '●',
            'square' => '■',
            'triangle' => '▲',
            'star' => '★',
            default => '●',
        };
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set default marker color based on risk level if available
            if (empty($model->marker_color) && $model->skinLesion) {
                $model->marker_color = $model->skinLesion->risk_color;
            }

            // Default color if no risk level
            if (empty($model->marker_color)) {
                $model->marker_color = '#FF0000';
            }
        });
    }
}
