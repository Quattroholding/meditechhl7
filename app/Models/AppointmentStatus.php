<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatus extends BaseModel
{
    protected $table='appointment_status';
    protected $fillable=['appointment_id','user_id','status','previous_status','observation'];

    public function appointment(){
        return $this->belongsTo(Appoinment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }


}
