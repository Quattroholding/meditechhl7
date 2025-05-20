<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProcedure extends BaseModel
{
    protected $table='user_procedures';
    protected $fillable=['user_id','client_id','code','description','cpt_code','current_price','type','active'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function cpt(){
        return $this->belongsTo(CptCode::class,'cpt_code');
    }

    public function getFullNameAttribute(){

        if(!empty($this->cpt_code)) return $this->cpt->full_name.' ($'.number_format($this->current_price, 2).')';

        return $this->code.' | '.$this->description.' ($'.number_format($this->current_price, 2).')';
    }

    function formato_dinero($value,$moneda='$') {
        return $moneda. number_format($value, 2);
    }
}
