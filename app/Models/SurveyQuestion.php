<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends BaseModel
{
    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
        'is_required',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyQuestionResponse::class);
    }

    public function getOptionsListAttribute()
    {
        return $this->options ? collect($this->options) : collect();
    }
}
