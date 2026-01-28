<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Client extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group',
        'ruc',
        'dv',
        'long_name',
        'email',
        'whatsapp',
        'logo',
        'package_id',
        'subscription_billing_day',
        'referred_by_client_id',
        'referral_code_used',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (! $client->yappy_code) {
                $client->yappy_code = strtoupper(Str::random(7));
            }
        });
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_clients');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_clients');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function theme()
    {
        return $this->hasOne(ClientTheme::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withDefault(['name' => '']);
    }

    // Subscription relationships
    public function subscription()
    {
        return $this->hasOne(ClientSubscription::class)->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(ClientSubscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(ClientInvoice::class);
    }

    public function discounts()
    {
        return $this->hasMany(SubscriptionDiscount::class);
    }

    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(Client::class, 'referred_by_client_id');
    }

    public function referrals()
    {
        return $this->hasMany(ClientReferral::class, 'referrer_client_id');
    }

    public function preferences()
    {
        return $this->hasMany(ClientPreference::class);
    }

    public function getFullNameAttribute($attr)
    {
        return $attr->name; // Change the format to whichever you desire
    }

    public function getProfileNameAttribute()
    {
        $path = url('assets/img/profiles/avatar-02.jpg');
        if (! empty($this->logo)) {
            $path = url('storage/'.$this->logo);
        }

        return '<div class="profile-image"><img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">'.$this->name.'</div>';
    }
}
