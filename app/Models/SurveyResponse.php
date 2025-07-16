<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SurveyResponse extends BaseModel
{
    protected $fillable = [
        'survey_id',
        'patient_id',
        'token',
        'submitted_at',
        'responses',
        'status'
    ];

    protected $casts = [
        'responses' => 'array',
        'submitted_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(32);
            }
        });
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function questionResponses(): HasMany
    {
        return $this->hasMany(SurveyQuestionResponse::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' && !is_null($this->submitted_at);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('survey.public', $this->token);
    }
}
