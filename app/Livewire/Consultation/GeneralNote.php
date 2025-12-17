<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Component;

class GeneralNote extends Component
{
    public $general_note;

    public $encounter_id;

    public $encounter;

    public $section_name;

    public $section_id;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->general_note = $this->encounter->general_note;

    }

    public function render()
    {
        return view('livewire.consultation.general-note');
    }

    public function updatedGeneralNote()
    {
        $this->save();
    }

    public function save()
    {
        $key = 'general_note';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->encounter->general_note = $this->general_note;
            $this->encounter->save();

            $this->dispatch('saved-'.$key);

            // Emitir evento al componente padre para calcular si el button finished se debe habilitar
            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,  $e->getMessage());
        }
    }
}
