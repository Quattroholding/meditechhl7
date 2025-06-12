<?php

namespace App\Models;

use App\Models\Scopes\ConsultingRoomScope;
use Illuminate\Database\Eloquent\Model;

class ConsultingRoom extends BaseModel
{
    protected $fillable=['id','branch_id','name','number','floor','active'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ConsultingRoomScope());
    }

    public function branch(){
        return $this->belongsTo(Branch::class)->withDefault([
            'name'=>'',
        ]);
    }

    public function getFullNameBranchAttribute(){
        return $this->name.' ('.$this->branch->name.')';
    }

    public function getBranchNameAttribute(){
        return $this->branch->name;
    }
}
