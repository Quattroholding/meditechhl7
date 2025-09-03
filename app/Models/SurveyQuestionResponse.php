<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestionResponse extends BaseModel
{
    protected $fillable = [
        'survey_response_id',
        'survey_question_id',
        'answer_text',
        'answer_data',
    ];

    protected $casts = [
        'answer_data' => 'array',
    ];

    public function surveyResponse(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }
}
