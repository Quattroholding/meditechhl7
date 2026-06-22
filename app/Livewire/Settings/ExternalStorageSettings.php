<?php

namespace App\Livewire\Settings;

use App\Models\ClientPreference;
use Livewire\Component;

class ExternalStorageSettings extends Component
{
    public bool $enabled = false;

    public string $provider = 'dropbox';

    public bool $isConnected = false;

    public ?string $accountInfo = null;

    public ?string $expiresAt = null;

    public function mount(): void
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            return;
        }

        $config = ClientPreference::getExternalStorageConfig($client->id);

        if ($config) {
            $this->enabled = $config['enabled'] ?? false;
            $this->provider = $config['provider'] ?? 'dropbox';
            $this->isConnected = isset($config['access_token']) && isset($config['refresh_token']);
            $this->accountInfo = $config['account_id'] ?? null;
            $this->expiresAt = $config['expires_at'] ?? null;
        }
    }

    public function connectDropbox(): void
    {
        $this->redirect(route('admin.dropbox.redirect'), navigate: false);
    }

    public function disconnectDropbox(): void
    {
        $this->redirect(route('admin.dropbox.disconnect'), navigate: false);
    }

    public function toggleEnabled(): void
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            session()->flash('error', 'No se ha seleccionado un cliente');

            return;
        }

        if (! $this->isConnected && $this->enabled) {
            session()->flash('error', 'Debe conectar con Dropbox primero');
            $this->enabled = false;

            return;
        }

        try {
            $config = ClientPreference::getExternalStorageConfig($client->id);

            if ($config) {
                $config['enabled'] = $this->enabled;
                ClientPreference::setExternalStorageConfig($client->id, $config);

                session()->flash('success', $this->enabled ? 'Almacenamiento externo habilitado' : 'Almacenamiento externo deshabilitado');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la configuración: '.$e->getMessage());
            $this->enabled = ! $this->enabled; // Revert
        }
    }

    public function render()
    {
        return view('livewire.settings.external-storage-settings');
    }
}
