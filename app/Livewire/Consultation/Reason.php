<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Component;

class Reason extends Component
{
    public $reason;

    public $encounter_id;

    public $encounter;

    public $section_name;

    public $section_id;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->reason = $this->encounter->reason;

    }

    public function render()
    {
        return view('livewire.consultation.reason');
    }

    public function updatedReason()
    {
        $this->save();
    }


    public function save()
    {
        $key = 'reason';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->encounter->reason = $this->reason;
            $this->encounter->save();

            $this->dispatch('saved-'.$key);

            // Emitir evento al componente padre para calcular si el button finished se debe habilitar
            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,$e->getMessage());
        }
    }
}
