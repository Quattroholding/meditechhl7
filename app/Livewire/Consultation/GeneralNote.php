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

    public $saving = false;

    public $saved = false;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->general_note = $this->encounter->general_note;

    }
    public function render()
    {
        return view('livewire.consultation.general-note');
    }

    public function save()
    {
        $this->saved = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            // Ejemplo: YourModel::updateOrCreate(['id' => $this->modelId], ['content' => $this->content]);
            $this->encounter->general_note = $this->general_note;
            $this->encounter->save();
            // Simular tiempo de guardado
            sleep(1);
            $this->saved = true;

            // Emitir evento al componente padre para calcular si el button finished se debe habilitar
            $this->dispatch('findFinishedButtonStatus');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: '.$e->getMessage());
        }
    }

    public function updatedReason()
    {
        $this->saved = false;

    }
}
