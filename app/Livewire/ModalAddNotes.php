<?php

namespace App\Livewire;

use App\Models\Note;
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

    public function render()
    {
        return view('livewire.modal-add-notes');
    }

    #[On('openModal')]
    public function openModal($patient_id=null,$practitioner_id=null,$encounter_id=null)
    {
        $this->note='';
        $this->patient_id=$patient_id;
        $this->practitioner_id=$practitioner_id;
        $this->encounter_id=$encounter_id;
        $this->showModal=true;
    }

    public function saveNote(){

        Note::create([
            'user_id'=>auth()->user()->id,
            'practitioner_id'=>$this->practitioner_id,
            'patient_id'=>$this->patient_id,
            'encounter_id'=>$this->encounter_id,
            'note'=>$this->note,
        ]);
        $this->note='';
        $this->showModal=false;

        $this->dispatch('showToastr',
            type: 'success',
            message: '¡Guardado exitosamente!'
        );
    }

    public function closeModal(){
        $this->showModal=false;
    }
}
