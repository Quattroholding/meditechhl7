<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientClient extends Model
{
    //
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
