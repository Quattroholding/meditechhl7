<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

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
        'whatsapp_phone',
        'active',
        'created_by',
        'register_source',
        'validation_status',
        'validated_at',
        'validated_by',
        'rejection_reason',
        'inactive_at',
        'inactivate_by',
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
            'validated_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
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

    public function getCurrentClient()
    {
        // Check if we're in a session context (web request with authenticated user)
        if (auth()->check() && auth()->user()->id === $this->id && session()->has('client')) {
            return session()->get('client_'.$this->id);
        }

        // For API contexts or when session is not available, use default client
        if ($this->default_client_id) {
            $client = Client::find($this->default_client_id);

            // Store in session only if we're the authenticated user
            if (auth()->check() && auth()->user()->id === $this->id) {
                session(['client_'.$this->id => $client]);
            }

            return $client;
        }

        return null;
    }

    public function getFullNameAttribute()
    {
        if ($this->hasRole('doctor') && $this->practitioner) {
            /*$prefix='Dr ';
            if($this->practitioner->gender =='female')
                $prefix='Dra ';*/
            $gender = $this->practitioner->gender;
            if ($gender) {
                $prefix = $gender == 'female' ? 'Dra. ' : 'Dr. ';
            }
        } else {
            $prefix = '';
        }

        return $prefix.$this->first_name.' '.$this->last_name;
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'user_clients');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeActiveAgent($query)
    {
        return $query->whereHas('clients', function ($query) {
            $query->whereHas('subscription', function ($query) {
                $query->whereIn('client_subscriptions.status', ['active', 'trial']);
                $query->whereHas('package', function ($query) {
                    $query->where('packages.agent_available', true);
                });
            });
        });
    }

    public function scopePackage($query, $packageId)
    {
        return $query->whereHas('clients', function ($query) use ($packageId) {
            $query->whereHas('subscription', function ($query) use ($packageId) {
                $query->where('package_id', $packageId);
            });
        });
    }

    public function activate()
    {
        $this->update(['active' => true]);
    }

    public function deactivate()
    {
        $this->update(['active' => false, 'inactive_at' => now(), 'inactivate_by' => auth()->id()]);
    }

    public function isActive()
    {
        return $this->active;
    }

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

    public function procedures()
    {
        return $this->hasMany(UserProcedure::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function lastSession()
    {
        return $this->hasOne(Session::class)->orderBy('last_activity', 'desc');
    }

    public function getProfileNameAttribute()
    {

        $prefix = '';
        $path = url('assets/img/profiles/avatar-02.jpg');
        if ($this->profile_picture) {
            $path = url('storage/'.$this->profile_picture);
        }

        if ($this->hasRole('doctor') && $this->practitioner) {
            /*$prefix='Dr ';
            if($this->practitioner->gender =='female')
             $prefix='Dra ';*/
            // dd($this->hasRole('doctor'), $this->practitioner->gender);
            $gender = $this->practitioner->gender;
            if ($gender) {
                $prefix = $gender == 'female' ? 'Dra. ' : 'Dr. ';
            }
        }

        return '<div class="profile-image">
                  <a href="'.url('patient/'.$this->id.'/pofile').'" class= "text-base">
                                        <img width="28" height="28" src="'.$path.'" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        '.$prefix.$this->first_name.' '.$this->last_name.'
                                    </a>
                    </div>';
    }

    public function workingHours()
    {
        return $this->hasMany(UserWorkingHour::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id')->where('table_name', $this->getTable());
    }

    public function avatar()
    {
        return $this->files()->whereType('avatar')->latest()->first();
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopePendingValidation($query)
    {
        return $query->where('register_source', 'app')
            ->where('active', false)
            ->where(function ($q) {
                $q->whereNull('validation_status')
                    ->orWhere('validation_status', 'pending');
            });
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

    public function getCurrentClientTotUsersCreated()
    {

        return $this->getCurrentClient()->users()->role('recepcionista')->count() +
            $this->getCurrentClient()->users()->role('admin client')->count() + $this->getCurrentClient()->users()->role('doctor')->count();
    }

    // === Subscription Status Methods ===

    /**
     * Get the current client's active subscription
     */
    public function getActiveSubscription()
    {
        $client = $this->getCurrentClient();

        return $client?->subscription;
    }

    /**
     * Check if user's client has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return false;
        }

        return in_array($subscription->status->value, ['active', 'trial']);
    }

    /**
     * Check if subscription is suspended
     */
    public function hasSubscriptionSuspended(): bool
    {
        $subscription = $this->getActiveSubscription();

        return $subscription && $subscription->status->value === 'suspended';
    }

    /**
     * Check if subscription is expired
     */
    public function hasSubscriptionExpired(): bool
    {
        $subscription = $this->getActiveSubscription();

        return $subscription && $subscription->status->value === 'expired';
    }

    /**
     * Check if subscription is pending activation (awaiting first payment)
     */
    public function hasSubscriptionPendingActivation(): bool
    {
        $subscription = $this->getActiveSubscription();

        return $subscription && $subscription->status->value === 'pending_activation';
    }

    /**
     * Check if subscription is past due
     */
    public function hasSubscriptionPastDue(): bool
    {
        $subscription = $this->getActiveSubscription();

        return $subscription && $subscription->status->value === 'past_due';
    }

    /**
     * Check if user can schedule appointments based on subscription status
     */
    public function canScheduleAppointments(): bool
    {
        // If no subscription system for this client, allow (legacy clients)
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return true; // No subscription required
        }

        // Check if subscription is active or in trial
        if (! in_array($subscription->status->value, ['active', 'trial'])) {
            return false;
        }

        // Check if appointments limit has been reached
        if ($this->hasReachedAppointmentsLimit()) {
            return false;
        }

        return true;
    }

    /**
     * Get subscription status message for user
     */
    public function getSubscriptionStatusMessage(): ?string
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return null;
        }

        // Check if appointments limit is reached first (higher priority)
        if ($this->hasReachedAppointmentsLimit()) {
            $limit = $this->getAppointmentsLimit();
            $currentCount = $this->getAppointmentsCountInCurrentPeriod();
            $periodEnd = $subscription->current_period_ends_at?->format('d/m/Y') ?? 'fin del período';

            return "Ha alcanzado el límite de {$limit} citas para su plan actual ({$currentCount}/{$limit} citas agendadas este período). Para continuar agendando citas, considere actualizar su plan de suscripción a uno superior o espere hasta el {$periodEnd} cuando se renueve su límite mensual.";
        }

        return match ($subscription->status->value) {
            'active' => null, // No message needed for active subscriptions
            'trial' => $subscription->trial_ends_at
                ? 'Período de prueba activo hasta el '.$subscription->trial_ends_at->format('d/m/Y')
                : 'Período de prueba activo',
            'pending_activation' => 'Suscripción pendiente de activación. Debe realizar el pago de la factura pendiente.',
            'suspended' => 'Su suscripción está suspendida por falta de pago. Regularice su situación para continuar usando el sistema.',
            'past_due' => 'Tiene facturas vencidas. Por favor realice el pago para evitar la suspensión del servicio.',
            'expired' => 'Su suscripción ha expirado. Contacte con soporte para reactivar el servicio.',
            'cancelled' => 'Su suscripción ha sido cancelada.',
            default => 'Estado de suscripción: '.$subscription->status->label(),
        };
    }

    /**
     * Get days remaining until subscription action needed
     */
    public function getDaysUntilSubscriptionAction(): ?int
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return null;
        }

        // If suspended, check days until expiration
        if ($subscription->status->value === 'suspended') {
            $expirationDays = config('subscriptions.expiration_after_suspension_days', 30);
            $daysSinceSuspension = now()->diffInDays($subscription->updated_at);

            return max(0, $expirationDays - $daysSinceSuspension);
        }

        // If past due, check days until suspension
        if ($subscription->status->value === 'past_due') {
            $invoice = $subscription->currentInvoice;
            if ($invoice) {
                $gracePeriodDays = config('subscriptions.grace_period_days', 7);
                $daysPastDue = now()->diffInDays($invoice->due_date);

                return max(0, $gracePeriodDays - $daysPastDue);
            }
        }

        // If in trial, show days until trial ends
        if ($subscription->status->value === 'trial' && $subscription->trial_ends_at) {
            return now()->diffInDays($subscription->trial_ends_at, false);
        }

        return null;
    }

    /**
     * Get subscription alert type (success, warning, danger)
     */
    public function getSubscriptionAlertType(): ?string
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return null;
        }

        // Check if appointments limit is reached first
        if ($this->hasReachedAppointmentsLimit()) {
            return 'warning';
        }

        return match ($subscription->status->value) {
            'active' => null,
            'trial' => 'info',
            'pending_activation' => 'warning',
            'past_due' => 'warning',
            'suspended' => 'danger',
            'expired' => 'danger',
            'cancelled' => 'danger',
            default => 'info',
        };
    }

    /**
     * Get the count of appointments in the current subscription period
     */
    public function getAppointmentsCountInCurrentPeriod(): int
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return 0;
        }

        $client = $this->getCurrentClient();

        if (! $client) {
            return 0;
        }

        // Count appointments created in the current subscription period
        return \App\Models\Appointment::where('client_id', $client->id)
            ->whereBetween('created_at', [
                $subscription->current_period_starts_at ?? now()->startOfMonth(),
                $subscription->current_period_ends_at ?? now()->endOfMonth(),
            ])
            ->count();
    }

    /**
     * Get the appointments limit from the current subscription package
     */
    public function getAppointmentsLimit(): ?int
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return null;
        }

        return $subscription->package->appointments_limit;
    }

    /**
     * Check if the user has reached the appointments limit
     */
    public function hasReachedAppointmentsLimit(): bool
    {
        $limit = $this->getAppointmentsLimit();

        // If limit is null, there's no limit (infinite)
        if ($limit === null) {
            return false;
        }

        $currentCount = $this->getAppointmentsCountInCurrentPeriod();

        return $currentCount >= $limit;
    }

    /**
     * Get remaining appointments in current period
     */
    public function getRemainingAppointments(): ?int
    {
        $limit = $this->getAppointmentsLimit();

        // If limit is null, return null (infinite)
        if ($limit === null) {
            return null;
        }

        $currentCount = $this->getAppointmentsCountInCurrentPeriod();

        return max(0, $limit - $currentCount);
    }
}
