<?php

namespace App\Models;

use App\Models\Accounting\AccountingAccount;
use App\Models\Finance\CostCenter;

class UserProcedure extends BaseModel
{
    protected $table = 'user_procedures';

    protected $fillable = ['user_id', 'client_id', 'code', 'description', 'cpt_code', 'current_price', 'type', 'active', 'accounting_account_id', 'cost_center_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function cpt()
    {
        return $this->belongsTo(CptCode::class, 'cpt_code');
    }

    public function accountingAccount()
    {
        return $this->belongsTo(AccountingAccount::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function getFullNameAttribute()
    {

        if (! empty($this->cpt_code)) {
            return $this->cpt->full_name.' ($'.number_format($this->current_price, 2).')';
        }

        return $this->code.' | '.$this->description.' ($'.number_format($this->current_price, 2).')';
    }

    public function formato_dinero($value, $moneda = '$')
    {
        return $moneda.number_format($value, 2);
    }
}
