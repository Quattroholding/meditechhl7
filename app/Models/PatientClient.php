<?php

namespace App\Models;

class PatientClient extends BaseModel
{
    protected $fillable = ['patient_id', 'client_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
