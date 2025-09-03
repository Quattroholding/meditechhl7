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
    // Solo el doctor puede escuchar su propio canal
    return $user->hasRole('doctor') &&
           $user->practitioner &&
           (int) $user->practitioner->id === (int) $doctorId;
});
