<?php

namespace App\Livewire\Doctor;

use App\Models\ClinicalImpression;
use Livewire\Component;

class NotesByPractitioner extends Component
{
    public $notes;

    public function mount()
    {
        // No cargar datos aquí, esperar la llamada explícita a loadData()
    }

    public function loadData()
    {
        $this->getNotes();
    }

    public function render()
    {
        return view('livewire.doctor.notes-by-practitioner');
    }

    public function getNotes()
    {
        // $this->doctor = auth()->user()->practitioner->id;
        $this->notes = ClinicalImpression::wherePractitionerId(auth()->user()->practitioner->id)->get();
        // dd($this->doctor, $completed);
    }
}
