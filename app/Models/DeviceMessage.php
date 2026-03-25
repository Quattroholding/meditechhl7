<?php

namespace App\Models;

class DeviceMessage extends BaseModel
{
    protected $fillable = ['clinic_id', 'device_serial', 'message_control_id', 'device_serial', 'message_type'];
}
