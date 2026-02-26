<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceMessage extends BaseModel
{
    protected $fillable = ['clinic_id', 'device_serial', 'message_control_id', 'device_serial', 'message_type'];
}
