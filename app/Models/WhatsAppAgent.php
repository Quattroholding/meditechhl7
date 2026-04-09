<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppAgent extends BaseModel
{
    protected $table = 'whatsapp_agents';

    protected $fillable = [
        'phone_number_id',
        'type',
        'webhook_url',
        'description',
        'active'
    ];

}
