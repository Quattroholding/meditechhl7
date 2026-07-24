<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class WaitlistSettings extends Component
{
    public $autoAssignEnabled = false;

    public function mount()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        // Get current setting (default is false for manual assignment)
        $this->autoAssignEnabled = $client->getSettings('waitlist_auto_assign', false);
    }

    public function toggleAutoAssign()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            $this->dispatch('error', 'No tiene un cliente asociado');

            return;
        }

        try {
            $client->setSettings(
                'waitlist_auto_assign',
                $this->autoAssignEnabled,
                'Configuración de asignación automática de slots de lista de espera'
            );

            $message = $this->autoAssignEnabled
                ? 'Asignación automática de lista de espera habilitada'
                : 'Asignación manual de lista de espera habilitada';

            $this->dispatch('success', $message);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Error al actualizar la configuración: '.$e->getMessage());
            $this->autoAssignEnabled = ! $this->autoAssignEnabled;
        }
    }

    public function render()
    {
        return view('livewire.settings.waitlist-settings');
    }
}
