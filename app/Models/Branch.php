<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends BaseModel
{
    use HasFactory;

    protected $fillable = ['client_id', 'name', 'phone', 'address', 'type', 'active', 'country_id', 'state_id'];

    // ESTE ES EL FILTRO GLOBAL POR TIPO DE ROL DE USUARIO
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function consultingRooms()
    {
        return $this->hasMany(ConsultingRoom::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function workingHours()
    {
        return $this->hasMany(UserWorkingHour::class, 'branch_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function getClientNameAttribute()
    {
        return $this->client->name;
    }

    public function getCountryNameAttribute()
    {
        return $this->country ? $this->country->name : '-';
    }

    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : '-';
    }
}
