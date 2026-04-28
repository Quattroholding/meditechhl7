<?php

namespace App\Livewire\User;

use App\Models\Client;
use App\Models\TwoFactorAuditLog;
use App\Notifications\TwoFactorDisabledNotification;
use App\Notifications\TwoFactorEnabledNotification;
use App\Services\FileService;
use Exception;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use PragmaRX\Google2FA\Google2FA;

class Profile extends Component
{
    use WithFileUploads;

    public $data;

    public $client_selected;

    public $clients;

    #[Validate('image|max:1024')] // 1MB Max
    public $avatar;

    // 2FA Properties
    public $showingQrCode = false;

    public $showingRecoveryCodes = false;

    public $recoveryCodes = [];

    public $twoFactorCode = '';

    public $twoFactorPassword = '';

    public $qrCode = '';

    public $enabled2FA = false;

    public $confirming2FA = false;

    public $required2FA = false;

    public function mount()
    {
        $this->data = auth()->user();
        $this->client_selected = $this->data->clients()->pluck('client_id')->toArray();
        $this->clients = Client::pluck('name', 'id')->toArray();

        if (! $this->data->hasRole('admin')) {
            $this->clients = $this->data->clients()->pluck('name', 'client_id')->toArray();
        }

        // Initialize 2FA state
        $this->enabled2FA = auth()->user()->hasTwoFactorEnabled();
        $this->required2FA = auth()->user()->requiresTwoFactor();
        $this->recoveryCodes = [];
    }

    public function updatedAvatar()
    {
        $service = new FileService;
        $data['record_id'] = $this->data->id;
        $data['folder'] = 'users';
        $data['type'] = 'avatar';
        $service->guardarArchivos([$this->avatar], $data);
        $this->data->update(['profile_picture' => $this->data->avatar()->path]);
    }

    // 2FA Methods
    public function enableTwoFactor()
    {
        logger('=== enableTwoFactor CALLED IN PROFILE ===');

        $this->validate([
            'twoFactorPassword' => 'required|current_password',
        ], [
            'twoFactorPassword.required' => 'La contraseña es obligatoria.',
            'twoFactorPassword.current_password' => 'La contraseña no es correcta.',
        ]);

        try {
            $user = auth()->user();
            $enable = app(EnableTwoFactorAuthentication::class);
            $enable($user);

            $this->qrCode = $user->twoFactorQrCodeSvg();
            $this->showingQrCode = true;
            $this->confirming2FA = true;
            $this->twoFactorPassword = '';

            session()->flash('message', 'Escanea el código QR con tu aplicación de autenticación.');
        } catch (Exception $e) {
            $this->addError('twoFactorPassword', 'Error al activar 2FA: '.$e->getMessage());
        }
    }

    public function confirmTwoFactor()
    {
        $this->validate([
            'twoFactorCode' => 'required|digits:6',
        ]);

        try {
            $user = auth()->user();
            $google2fa = app(Google2FA::class);
            $valid = $google2fa->verifyKey(
                decrypt($user->two_factor_secret),
                $this->twoFactorCode
            );

            if (! $valid) {
                $this->addError('twoFactorCode', 'El código ingresado es inválido.');

                return;
            }

            $confirm = app(ConfirmTwoFactorAuthentication::class);
            $confirm($user);

            $this->recoveryCodes = $user->recoveryCodes();
            $this->showingRecoveryCodes = true;
            $this->showingQrCode = false;
            $this->confirming2FA = false;
            $this->enabled2FA = true;
            $this->twoFactorCode = '';

            TwoFactorAuditLog::log($user->id, 'enabled');
            $user->notify(new TwoFactorEnabledNotification);

            session()->flash('success', '¡Autenticación de dos factores activada! Guarda tus códigos de recuperación.');
        } catch (Exception $e) {
            $this->addError('twoFactorCode', 'Error al confirmar 2FA: '.$e->getMessage());
        }
    }

    public function disableTwoFactor()
    {
        $this->validate([
            'twoFactorPassword' => 'required|current_password',
        ]);

        if ($this->required2FA) {
            $this->addError('twoFactorPassword', 'Tu rol requiere 2FA. No puedes desactivarlo.');

            return;
        }

        try {
            $user = auth()->user();
            $disable = app(DisableTwoFactorAuthentication::class);
            $disable($user);

            $this->enabled2FA = false;
            $this->twoFactorPassword = '';
            $this->showingQrCode = false;
            $this->showingRecoveryCodes = false;

            TwoFactorAuditLog::log($user->id, 'disabled');
            $user->notify(new TwoFactorDisabledNotification);

            session()->flash('success', 'Autenticación de dos factores desactivada.');
        } catch (Exception $e) {
            $this->addError('twoFactorPassword', 'Error al desactivar 2FA: '.$e->getMessage());
        }
    }

    public function regenerateRecoveryCodes()
    {
        $this->validate([
            'twoFactorPassword' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (! $user->hasTwoFactorEnabled()) {
            $this->addError('twoFactorPassword', 'Primero debes activar 2FA.');

            return;
        }

        try {
            $generate = app(GenerateNewRecoveryCodes::class);
            $generate($user);

            $this->recoveryCodes = $user->recoveryCodes();
            $this->showingRecoveryCodes = true;
            $this->twoFactorPassword = '';

            TwoFactorAuditLog::log($user->id, 'recovery_codes_regenerated');

            session()->flash('success', 'Códigos de recuperación regenerados. Guárdalos en un lugar seguro.');
        } catch (Exception $e) {
            $this->addError('twoFactorPassword', 'Error al regenerar códigos: '.$e->getMessage());
        }
    }

    public function cancelSetup()
    {
        try {
            $user = auth()->user();
            $disable = app(DisableTwoFactorAuthentication::class);
            $disable($user);

            $this->showingQrCode = false;
            $this->confirming2FA = false;
            $this->twoFactorCode = '';
            $this->twoFactorPassword = '';

            session()->flash('message', 'Configuración de 2FA cancelada.');
        } catch (Exception $e) {
            $this->addError('twoFactorPassword', 'Error al cancelar: '.$e->getMessage());
        }
    }

    public function render()
    {

        return view('livewire.user.profile');
    }
}
