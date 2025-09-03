<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistoryLog extends Model
{
    protected $table = 'status_history_logs';

    protected $fillable = [
        'table_name',
        'record_id',
        'old_status',
        'new_status',
        'observation',
        'change_type',
        'user_id',
        'model_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
