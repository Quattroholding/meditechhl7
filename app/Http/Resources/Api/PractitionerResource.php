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
            'qualification' => $this->qualification,
            'license_number' => $this->license_number,
            'specialties' => $this->specialties ? $this->specialties->map(function ($specialty) {
                return [
                    'id' => $specialty->id,
                    'name' => $specialty->name,
                    'description' => $specialty->description ?? null,
                ];
            }) : [],
            'clients' => $this->user && $this->user->clients ?
                $this->user->clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'code' => $client->code ?? null,
                    ];
                }) : [],
            'insurances' => $this->insuranceCompanies ? $this->insuranceCompanies->map(function ($insurance) {
                return [
                    'id' => $insurance->id,
                    'name' => $insurance->name,
                    'accepts'=>$insurance->pivot->name ?? null,
                    'custom_coverage_percentage'=>$insurance->pivot->custom_coverage_percentage ?? null,
                    'custom_copay_amount'=>$insurance->pivot->custom_copay_amount ?? null,
                    'notes'=>$insurance->pivot->notes ?? null
                ];
            }) : [],
            'profile_photo' => $this->avatar() ?
                config('app.url').'/storage/'.$this->avatar()->path : '',
            'active' => $this->active,
            'next_week_schedule' => $this->when(isset($this->next_week_schedule), $this->next_week_schedule),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
