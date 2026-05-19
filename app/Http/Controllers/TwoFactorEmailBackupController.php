<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorAuditLog;
use App\Models\User;
use App\Services\TwoFactorEmailBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorEmailBackupController extends Controller
{
    public function __construct(
        protected TwoFactorEmailBackupService $backupService
    ) {}

    /**
     * Request an email backup code during 2FA challenge.
     */
    public function requestEmailCode(Request $request): JsonResponse
    {
        // Get user ID from session (set during 2FA challenge)
        $userId = session('login.id');

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión inválida. Por favor, inicia sesión nuevamente.',
            ], 401);
        }

        // Rate limiting
        $key = 'email-backup-request:'.$userId;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => 'Demasiadas solicitudes. Intenta nuevamente en '.ceil($seconds / 60).' minutos.',
            ], 429);
        }

        RateLimiter::hit($key, 600); // 10 minutes

        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        try {
            $this->backupService->sendEmailBackupCode($user);

            TwoFactorAuditLog::log($user->id, 'email_backup_requested');

            return response()->json([
                'success' => true,
                'message' => 'Código enviado a '.$this->maskEmail($user->email),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el código. Intenta nuevamente.',
            ], 500);
        }
    }

    /**
     * Verify email backup code during 2FA challenge.
     */
    public function verifyEmailCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('login.id');

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión inválida.',
            ], 401);
        }

        // Rate limiting for verification attempts
        $key = 'email-backup-verify:'.$userId;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            TwoFactorAuditLog::log($userId, 'email_backup_rate_limited');

            return response()->json([
                'success' => false,
                'message' => 'Demasiados intentos fallidos. Espera 15 minutos.',
            ], 429);
        }

        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        if ($this->backupService->verifyEmailBackupCode($user, $request->code)) {
            // Code is valid, log in the user
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();

            // Clear rate limiter
            RateLimiter::clear($key);

            TwoFactorAuditLog::log($user->id, 'email_backup_success');

            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
            ]);
        }

        // Invalid code
        RateLimiter::hit($key, 900); // 15 minutes
        TwoFactorAuditLog::log($userId, 'email_backup_failed');

        return response()->json([
            'success' => false,
            'message' => 'Código inválido o expirado.',
        ], 422);
    }

    /**
     * Mask email address for display.
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $maskedName = substr($name, 0, 2).str_repeat('*', max(strlen($name) - 2, 0));

        return $maskedName.'@'.$domain;
    }
}
