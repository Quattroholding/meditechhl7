<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'default_client_id',
        'first_login_at',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'first_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function routeNotificationForMail($notification = null)
    {
        // Si estamos en testing, usar correo específico
        if (config('app.env') === 'testing' || config('mail.testing_mode', false)) {
            return config('mail.testing_practitioner_email', 'doctor.test@example.com');
        }

        return $this->email;
    }

    public function getCurrentClient(){

        if(session()->has('client')){
            return session()->get('client');
        }else{
            session(['client' => Client::find($this->default_client_id)]);
            return  Client::find($this->default_client_id);
        }
    }

    public function getFullNameAttribute() {
        if($this->hasRole('doctor')){
            /*$prefix='Dr ';
            if($this->practitioner->gender =='female')
                $prefix='Dra ';*/
            $gender=$this->practitioner->gender;
            if($gender)
            $prefix = $gender =='female' ? 'Dra. ' : 'Dr. ';
        }else{
            $prefix='';
        }

        return $prefix.$this->first_name . ' ' . $this->last_name;
    }

    public function clients(){ return $this->belongsToMany(Client::class,'user_clients'); }

    // Relación con paciente (si existe)
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    // Relación con médico (si existe)
    public function practitioner()
    {
        return $this->hasOne(Practitioner::class);
    }

    public function procedures(){
        return $this->hasMany(UserProcedure::class);
    }

    public function getProfileNameAttribute(){

        $prefix='';
        $path = url('assets/img/profiles/avatar-02.jpg');
        if($this->profile_picture) $path = url('storage/'.$this->profile_picture);

        if($this->hasRole('doctor')){
            /*$prefix='Dr ';
            if($this->practitioner->gender =='female')
             $prefix='Dra ';*/
            // dd($this->hasRole('doctor'), $this->practitioner->gender);
            $gender=$this->practitioner->gender;
            if($gender)
            $prefix = $gender =='female' ? 'Dra. ' : 'Dr. ';
        }

        return '<div class="profile-image">
                  <a href="'.url('patient/'.$this->id.'/pofile').'" >
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$prefix.$this->first_name.' '.$this->last_name.'
                                    </a>
                    </div>';
    }

    public function workingHours(){
        return $this->hasMany(UserWorkingHour::class);
    }

    public function files(){
        return $this->hasMany(File::class,'record_id')->where('table_name',$this->getTable());
    }

    public function avatar(){
        return $this->files()->whereType('avatar')->latest()->first();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

}
