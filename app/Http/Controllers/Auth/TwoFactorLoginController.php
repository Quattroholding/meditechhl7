<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorLoginController extends Controller
{
    /**
     * Show the two-factor authentication challenge view.
     */
    public function show(Request $request)
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Attempt to authenticate using a two-factor authentication code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = $request->session()->get('login.id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        // Verify the code
        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if (! $valid) {
            // Check if it's a recovery code
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            if (! in_array($request->code, $recoveryCodes ?? [])) {
                throw ValidationException::withMessages([
                    'code' => ['El código proporcionado es inválido.'],
                ]);
            }

            // Mark recovery code as used
            $user->replaceRecoveryCode($request->code);
        }

        // Login the user
        $remember = $request->session()->get('login.remember', false);
        Auth::login($user, $remember);

        // Clear 2FA session data
        $request->session()->forget(['login.id', 'login.remember']);

        $request->session()->regenerate();

        // Redirect based on role (reuse the method from LoginController)
        $loginController = new LoginController;

        return $loginController->redirectLoginByRole($user);
    }
}
