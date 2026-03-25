<?php

namespace App\Livewire;

use App\Models\ClinicalImpression;
use App\Models\Note;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalAddNotes extends Component
{
    #[Modelable]
    public $showModal;

    public $note;

    public $practitioner_id;

    public $patient_id;

    public $encounter_id;

    public $type = 'private';

    public function render()
    {
        return view('livewire.modal-add-notes');
    }

    #[On('openModal')]
    public function openModal($patient_id = null, $practitioner_id = null, $type = 'private', $encounter_id = null)
    {
        $this->note = '';
        $this->patient_id = $patient_id;
        $this->practitioner_id = $practitioner_id;
        $this->encounter_id = $encounter_id;
        $this->type = $type;
        $this->showModal = true;
    }

    public function saveNote()
    {
        if ($this->type == 'private') {
            Note::create([
                'user_id' => auth()->user()->id,
                'practitioner_id' => $this->practitioner_id,
                'patient_id' => $this->patient_id,
                'encounter_id' => $this->encounter_id,
                'note' => $this->note,
            ]);

            // Dispatch event to reload personal notes
            $this->dispatch('noteAdded', type: 'personal', patientId: $this->patient_id);
        } elseif ($this->type == 'medical') {
            ClinicalImpression::create([
                'patient_id' => $this->patient_id,
                'practitioner_id' => $this->practitioner_id,
                'description' => $this->note,
                'encounter_id' => $this->encounter_id,
                'fhir_id' => 'clinicalimpresion-'.Str::uuid(),
                'status' => 'completed',
            ]);

            // Dispatch event to reload medical notes
            $this->dispatch('noteAdded', type: 'medical', patientId: $this->patient_id);
        }

        $this->note = '';
        $this->showModal = false;

        $this->dispatch('showToastrModalAddNote',
            type: 'success',
            message: '¡Guardado exitosamente!'
        );
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
}
