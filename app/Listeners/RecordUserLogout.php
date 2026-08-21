<?php

namespace App\Listeners;

use App\Models\UserLogin;
use Illuminate\Auth\Events\Logout;

class RecordUserLogout
{
    public function handle(Logout $event): void
    {
        $sessionId = session()->getId();

        // Marcar el logout más reciente del usuario como logged_out
        UserLogin::where('user_id', $event->user->id)
            ->where('session_id', $sessionId)
            ->where('status', 'active')
            ->latest()
            ->first()
            ?->update([
                'logout_at' => now(),
                'status' => 'logged_out',
            ]);
    }
}
