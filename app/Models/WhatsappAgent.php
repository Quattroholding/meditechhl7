<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappAgent extends BaseModel
{
    use SoftDeletes;

    protected $table = 'whatsapp_agents';

    protected $fillable = [
        'phone_number_id',
        'type',
        'webhook_url',
        'description',
        'active',
        'client_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the client associated with this WhatsApp agent.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Determine if this agent is personalized for a specific client.
     */
    public function isPersonalized(): bool
    {
        return ! is_null($this->client_id);
    }
}
