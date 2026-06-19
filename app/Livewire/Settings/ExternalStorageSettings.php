<?php

namespace App\Livewire\Settings;

use App\Models\ClientPreference;
use App\Services\Storage\DropboxStorageProvider;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class ExternalStorageSettings extends Component
{
    public bool $enabled = false;

    public string $provider = 'dropbox';

    public string $dropbox_access_token = '';

    public bool $testingConnection = false;

    public ?string $connectionTestResult = null;

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
            $this->dropbox_access_token = ''; // No mostramos el token por seguridad
        }
    }

    public function testDropboxConnection(): void
    {
        $this->testingConnection = true;
        $this->connectionTestResult = null;

        try {
            if (empty($this->dropbox_access_token)) {
                $this->connectionTestResult = 'error:Por favor ingrese un token de acceso';
                $this->testingConnection = false;

                return;
            }

            $provider = new DropboxStorageProvider($this->dropbox_access_token);

            if ($provider->validateCredentials()) {
                $this->connectionTestResult = 'success:Conexión exitosa con Dropbox';
            } else {
                $this->connectionTestResult = 'error:No se pudo conectar con Dropbox. Verifique el token de acceso';
            }
        } catch (\Exception $e) {
            $this->connectionTestResult = 'error:Error al conectar con Dropbox: '.$e->getMessage();
        } finally {
            $this->testingConnection = false;
        }
    }

    public function saveSettings(): void
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            session()->flash('error', 'No se ha seleccionado un cliente');

            return;
        }

        try {
            $config = [
                'enabled' => $this->enabled,
                'provider' => $this->provider,
            ];

            if ($this->enabled && $this->provider === 'dropbox') {
                if (empty($this->dropbox_access_token)) {
                    session()->flash('error', 'Por favor ingrese un token de acceso de Dropbox');

                    return;
                }

                $provider = new DropboxStorageProvider($this->dropbox_access_token);

                if (! $provider->validateCredentials()) {
                    session()->flash('error', 'No se pudo validar el token de Dropbox. Por favor verifique que sea correcto.');

                    return;
                }

                $config['dropbox_access_token'] = Crypt::encryptString($this->dropbox_access_token);
            }

            ClientPreference::setExternalStorageConfig($client->id, $config);

            session()->flash('success', 'Configuración de almacenamiento externo guardada exitosamente');

            $this->dropbox_access_token = '';

            $this->dispatch('settings-saved');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la configuración: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.external-storage-settings');
    }
}
