<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentIllnesType extends Model
{
    protected $table='present_illnesse_types';
    protected $fillable=['type','value','value_esp','order','path_icon'];
}
