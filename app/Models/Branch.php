<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends BaseModel
{
    protected $fillable=['client_id','name','phone','address','type','active'];

    // ESTE ES EL FILTRO GLOBAL POR TIPO DE ROL DE USUARIO
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope());
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function consultingRooms(){
        return $this->hasMany(ConsultingRoom::class);
    }

    public function getClientNameAttribute(){
        return $this->client->name;
    }
}
