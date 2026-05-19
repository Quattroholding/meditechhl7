<?php

namespace App\Livewire\User;

use App\Models\TwoFactorAuditLog;
use App\Notifications\TwoFactorDisabledNotification;
use App\Notifications\TwoFactorEnabledNotification;
use Exception;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Component;

class TwoFactorAuthentication extends Component
{
    public $showingQrCode = false;

    public $showingRecoveryCodes = false;

    public $recoveryCodes = [];

    public $code = '';

    public $password = '';

    public $qrCode = '';

    public $enabled = false;

    public $confirming = false;

    public $required = false;

    public function mount()
    {
        $this->enabled = auth()->user()->hasTwoFactorEnabled();
        $this->required = auth()->user()->requiresTwoFactor();
        $this->recoveryCodes = collect();
    }

    public function enableTwoFactor()
    {
        logger('=== enableTwoFactor CALLED ===', [
            'user_id' => auth()->id(),
            'password_present' => ! empty($this->password),
        ]);

        $this->validate([
            'password' => 'required|current_password',
        ]);

        try {
            $user = auth()->user();

            // Use Fortify's EnableTwoFactorAuthentication action
            $enable = app(EnableTwoFactorAuthentication::class);
            $enable($user);

            // Get QR code (provided by Fortify's TwoFactorAuthenticatable trait)
            $this->qrCode = $user->twoFactorQrCodeSvg();
            $this->showingQrCode = true;
            $this->confirming = true;
            $this->password = '';

            session()->flash('message', 'Escanea el código QR con tu aplicación de autenticación.');
        } catch (Exception $e) {
            $this->addError('password', 'Error al activar 2FA: '.$e->getMessage());
        }
    }

    public function confirmTwoFactor()
    {
        $this->validate([
            'code' => 'required|digits:6',
        ]);

        try {
            $user = auth()->user();

            // Use Fortify's ConfirmTwoFactorAuthentication action (it verifies the code internally)
            $confirm = app(ConfirmTwoFactorAuthentication::class);

            try {
                $confirm($user, $this->code);
            } catch (Exception $e) {
                $this->addError('code', 'El código ingresado es inválido.');

                return;
            }

            // Get recovery codes (provided by Fortify's TwoFactorAuthenticatable trait)
            $this->recoveryCodes = collect($user->recoveryCodes());
            $this->showingRecoveryCodes = true;
            $this->showingQrCode = false;
            $this->confirming = false;
            $this->enabled = true;
            $this->code = '';

            // Log and notify
            TwoFactorAuditLog::log($user->id, 'enabled');
            $user->notify(new TwoFactorEnabledNotification);

            session()->flash('success', '¡Autenticación de dos factores activada! Guarda tus códigos de recuperación.');
        } catch (Exception $e) {
            $this->addError('code', 'Error al confirmar 2FA: '.$e->getMessage());
        }
    }

    public function disableTwoFactor()
    {
        $this->validate([
            'password' => 'required|current_password',
        ]);

        if ($this->required) {
            $this->addError('password', 'Tu rol requiere 2FA. No puedes desactivarlo.');

            return;
        }

        try {
            $user = auth()->user();

            // Use Fortify's DisableTwoFactorAuthentication action
            $disable = app(DisableTwoFactorAuthentication::class);
            $disable($user);

            $this->enabled = false;
            $this->password = '';
            $this->showingQrCode = false;
            $this->showingRecoveryCodes = false;

            // Log and notify
            TwoFactorAuditLog::log($user->id, 'disabled');
            $user->notify(new TwoFactorDisabledNotification);

            session()->flash('success', 'Autenticación de dos factores desactivada.');
        } catch (Exception $e) {
            $this->addError('password', 'Error al desactivar 2FA: '.$e->getMessage());
        }
    }

    public function regenerateRecoveryCodes()
    {
        $this->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (! $user->hasTwoFactorEnabled()) {
            $this->addError('password', 'Primero debes activar 2FA.');

            return;
        }

        try {
            // Use Fortify's GenerateNewRecoveryCodes action
            $generate = app(GenerateNewRecoveryCodes::class);
            $generate($user);

            // Get new recovery codes (provided by Fortify's TwoFactorAuthenticatable trait)
            $this->recoveryCodes = collect($user->recoveryCodes());
            $this->showingRecoveryCodes = true;
            $this->password = '';

            TwoFactorAuditLog::log($user->id, 'recovery_codes_regenerated');

            session()->flash('success', 'Códigos de recuperación regenerados. Guárdalos en un lugar seguro.');
        } catch (Exception $e) {
            $this->addError('password', 'Error al regenerar códigos: '.$e->getMessage());
        }
    }

    public function cancelSetup()
    {
        try {
            $user = auth()->user();

            // Use Fortify's DisableTwoFactorAuthentication action to cancel
            $disable = app(DisableTwoFactorAuthentication::class);
            $disable($user);

            $this->showingQrCode = false;
            $this->confirming = false;
            $this->code = '';
            $this->password = '';

            session()->flash('message', 'Configuración de 2FA cancelada.');
        } catch (Exception $e) {
            $this->addError('password', 'Error al cancelar: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.two-factor-authentication');
    }
}
