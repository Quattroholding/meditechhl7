<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorAuditLog;
use App\Models\User;
use App\Notifications\TwoFactorDisabledNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorAdminController extends Controller
{
    /**
     * Disable two-factor authentication for a user (admin override).
     */
    public function disableUserTwoFactor(Request $request, User $user): RedirectResponse
    {
        // Ensure only admins can do this
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (! $user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Este usuario no tiene 2FA activado.');
        }

        // Disable 2FA
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        // Log the admin override
        TwoFactorAuditLog::log(
            userId: $user->id,
            action: 'admin_override',
            performedBy: auth()->id(),
            reason: $request->reason
        );

        // Notify the user
        $user->notify(new TwoFactorDisabledNotification(
            disabledBy: auth()->user()->full_name,
            reason: $request->reason
        ));

        return back()->with('success', 'Autenticación de dos factores desactivada para '.$user->full_name);
    }

    /**
     * View a user's two-factor status.
     */
    public function viewUserTwoFactorStatus(User $user)
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $logs = TwoFactorAuditLog::where('user_id', $user->id)
            ->with('performer')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.users.two-factor-status', compact('user', 'logs'));
    }
}
