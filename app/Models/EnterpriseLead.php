<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EnterpriseLead extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'company_name',
        'number_of_doctors',
        'agent_preference',
        'number_of_branches',
        'current_system',
        'message',
        'status',
        'assigned_to',
        'source',
        'metadata',
        'contacted_at',
        'converted_to_client_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'contacted_at' => 'datetime',
        ];
    }

    // Relationships
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedClient()
    {
        return $this->belongsTo(Client::class, 'converted_to_client_id');
    }
}
