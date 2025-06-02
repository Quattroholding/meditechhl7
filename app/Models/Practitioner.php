<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Practitioner extends BaseModel
{

    use HasFactory;
    protected $fillable = [
        'fhir_id', 'identifier', 'name', 'given_name', 'family_name',
        'gender', 'birth_date', 'address', 'phone', 'email', 'active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean'
    ];

    // Relaciones
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(PractitionerQualification::class);
    }

    public function medicationRequests(): HasMany
    {
        return $this->hasMany(MedicationRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function avatar(){
        return $this->files()->whereType('avatar')->latest()->first();
    }

    public function getProfileNameAttribute(){
        $path = url('assets/img/profiles/avatar-02.jpg');
        if($this->avatar()) $path = url('storage/'.$this->avatar()->path);

        return '<div class="profile-image">
                  <a href="'.url('patient/'.$this->id.'/profile').'" >
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$this->name.'
                                    </a>
                    </div>';
    }

    public function getBirthDateAttribute($attr){
        return Carbon::parse($attr)->format('d-m-Y');
    }

    // Scope para obtener solo practicantes activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

}
