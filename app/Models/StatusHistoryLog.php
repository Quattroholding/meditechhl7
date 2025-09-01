<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistoryLog extends Model
{
    protected $table = 'status_history_logs';
    
    protected $fillable = [
        'model_type',
        'model_id',
        'field_name',
        'old_value',
        'new_value',
        'reason',
        'change_type',
        'user_id',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
