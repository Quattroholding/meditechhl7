<?php

namespace App\Actions;

use App\Models\Client;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatePractitionerUserAction
{
    public function execute(Practitioner $practitioner, bool $createActive = true): User
    {
        if ($practitioner->user_id) {
            throw ValidationException::withMessages([
                'practitioner' => 'El profesional ya tiene un usuario asignado.',
            ]);
        }

        if (! $practitioner->email) {
            throw ValidationException::withMessages([
                'email' => 'El profesional debe tener un email para crear un usuario.',
            ]);
        }

        if (User::where('email', $practitioner->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Ya existe un usuario con este email.',
            ]);
        }

        // $temporaryPassword = Str::random(12);

        $user = User::create([
            'first_name' => $practitioner->given_name ?? $practitioner->name,
            'last_name' => $practitioner->family_name ?? '',
            'email' => $practitioner->email,
            'password' => Hash::make('password'),
            'active' => $createActive,
            'whatsapp_phone' => $practitioner->phone,
        ]);

        $user->assignRole('doctor');
        $user->update(['default_client_id' => Client::first()->id]);

        // $user->temporary_password = $temporaryPassword;

        return $user;
    }
}
