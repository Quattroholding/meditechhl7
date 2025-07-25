<?php

namespace App\Livewire\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public $encounter_id;
    public $encounter;
    public $patient;
    public $appointment;

    public $encounter_sections;
    public $secciones;

    public function mount(){
        $encounter_sections_user = EncounterTemplate::whereUserId(Auth::getUser()->id)->get();

        if ($encounter_sections_user->count() > 0) {
            $this->encounter_sections = EncounterSection::whereIn('id', $encounter_sections_user->pluck('encounter_section_id'))->get();
        } else {
            $this->encounter_sections = EncounterSection::whereNull('category')->get();
        }

        $this->secciones = $this->encounter_sections->pluck('name_esp', 'id');

        $this->encounter = Encounter::find($this->encounter_id);

        $this->patient = Patient::find($this->encounter->patient_id);

        $this->appointment = Appointment::find($this->encounter->appointment_id);

    }

    public function render()
    {
        return view('livewire.consultation.create');
    }
}
