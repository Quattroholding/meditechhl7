<?php

namespace App\Models;

use App\Models\Scopes\SurveyResponsesScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SurveyResponse extends BaseModel
{
    protected $fillable = [
        'survey_id',
        'patient_id',
        'encounter_id',
        'practitioner_id',
        'client_id',
        'medical_speciality_id',
        'token',
        'submitted_at',
        'responses',
        'status',
    ];

    protected $casts = [
        'responses' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new SurveyResponsesScope);
    }

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

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class);
    }

    public function questionResponses(): HasMany
    {
        return $this->hasMany(SurveyQuestionResponse::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' && ! is_null($this->submitted_at);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('survey.public', $this->token);
    }
}
