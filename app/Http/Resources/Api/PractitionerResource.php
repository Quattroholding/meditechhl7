<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PractitionerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fhir_id' => $this->fhir_id,
            'name' => $this->name,
            'given_name' => $this->given_name,
            'family_name' => $this->family_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            // 'qualification' => $this->qualification,
            'license_number' => $this->license_number,
            'specialties' => $this->specialties ? $this->specialties->map(function ($specialty) {
                return [
                    'id' => $specialty->id,
                    'name' => $specialty->name,
                    'description' => $specialty->description ?? null,
                ];
            }) : [],
            'clients' => $this->user && $this->user->clients ? $this->user->clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'long_name' => $client->long_name,
                    'ruc' => $client->ruc,
                    'dv' => $client->dv,
                    'whatsapp' => $client->whatsapp,
                    'logo' => $client->logo ?? null,
                    '',
                ];
            }) : [],
            'insurances' => $this->insuranceCompanies ? $this->insuranceCompanies->map(function ($insurance) {
                return [
                    'id' => $insurance->id,
                    'name' => $insurance->name,
                    'accepts' => $insurance->pivot->name ?? null,
                    'custom_coverage_percentage' => $insurance->pivot->custom_coverage_percentage ?? null,
                    'custom_copay_amount' => $insurance->pivot->custom_copay_amount ?? null,
                    'notes' => $insurance->pivot->notes ?? null,
                ];
            }) : [],
            'working_hours' => $this->user->workingHours->map(function ($workingHour) {
                return [
                    'id' => $workingHour->id,
                    'day_of_week' => $workingHour->day_of_week,
                    'start_time' => $workingHour->start_time,
                    'end_time' => $workingHour->end_time,
                    'branch' => $workingHour->branch,
                ];
            }),
            'profile_photo' => $this->avatar() ? config('app.url').'/storage/'.$this->avatar()->path : '',
            'active' => $this->active,
            'appointments_this_month' => $this->appointments_this_month ?? 0,
            'subscription' => $this->when($this->user, function () {
                try {
                    $subscription = $this->user->getActiveSubscription();

                    if (! $subscription || ! $subscription->package) {
                        return null;
                    }

                    return [
                        'id' => $subscription->id,
                        'status' => $subscription->status->value,
                        'status_label' => $subscription->status->label(),
                        'package' => [
                            'id' => $subscription->package->id,
                            'name' => $subscription->package->name,
                            'description' => $subscription->package->description,
                            'base_price' => $subscription->package->base_price,
                            'billing_period' => $subscription->package->billing_period->value,
                            'appointments_limit' => $subscription->package->appointments_limit,
                        ],
                        'appointments_usage' => [
                            'limit' => $subscription->package->appointments_limit,
                            'used' => $this->user->getAppointmentsCountInCurrentPeriod(),
                            'remaining' => $this->user->getRemainingAppointments(),
                            'is_unlimited' => $subscription->package->appointments_limit === null,
                            'has_reached_limit' => $this->user->hasReachedAppointmentsLimit(),
                        ],
                        'period' => [
                            'starts_at' => $subscription->current_period_starts_at?->format('Y-m-d'),
                            'ends_at' => $subscription->current_period_ends_at?->format('Y-m-d'),
                            'next_billing_date' => $subscription->next_billing_date?->format('Y-m-d'),
                        ],
                        'can_schedule_appointments' => $this->user->canScheduleAppointments(),
                    ];
                } catch (\Exception $e) {
                    // If there's any error getting subscription data, return null
                    return null;
                }
            }),
            'next_week_schedule' => $this->when(isset($this->next_week_schedule), $this->next_week_schedule),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
