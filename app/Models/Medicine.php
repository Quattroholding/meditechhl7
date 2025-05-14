<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends BaseModel
{

    protected $table='medicines';

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function components(){
        return $this->hasMany(MedicineActiveComponent::class);
    }

    public function getFullNameAttribute(){
        return $this->generic_name." (".$this->type." ".$this->mgs." ".$this->mgs_type.")";
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }
}
