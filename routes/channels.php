<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para notificaciones del doctor
Broadcast::channel('doctor.{doctorId}', function ($user, $doctorId) {
    \Log::info('Broadcasting channel authorization attempt', [
        'channel' => 'doctor.'.$doctorId,
        'user_id' => $user->id,
        'user_email' => $user->email,
        'has_doctor_role' => $user->hasRole('doctor'),
        'has_practitioner' => $user->practitioner !== null,
        'practitioner_id' => $user->practitioner->id ?? null,
        'requested_doctor_id' => $doctorId,
    ]);

    // Solo el doctor puede escuchar su propio canal
    $authorized = $user->hasRole('doctor') &&
                  $user->practitioner &&
                  (int) $user->practitioner->id === (int) $doctorId;

    \Log::info('Broadcasting channel authorization result', [
        'channel' => 'doctor.'.$doctorId,
        'authorized' => $authorized,
    ]);

    return $authorized;
});
