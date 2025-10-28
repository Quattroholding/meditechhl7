<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'status',
        'client_id',
        'created_by',
        'trigger_point',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        // static::addGlobalScope(new ClientScope);

        static::saving(function ($survey) {
            // If this survey is being activated with a trigger_point
            if ($survey->is_active && $survey->trigger_point) {
                // Deactivate any other active surveys with the same trigger_point and client
                static::where('client_id', $survey->client_id)
                    ->where('trigger_point', $survey->trigger_point)
                    ->where('is_active', true)
                    ->when($survey->exists, function ($query) use ($survey) {
                        // Exclude the current survey if it's being updated
                        $query->where('id', '!=', $survey->id);
                    })
                    ->update(['is_active' => false]);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
