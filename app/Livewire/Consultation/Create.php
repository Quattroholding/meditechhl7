<?php

namespace App\Livewire\Consultation;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $encounter_id;

    public $encounter;

    public $patient;

    public $appointment;

    public $encounter_sections;

    public $secciones;

    public function mount()
    {
        $encounter_sections_user = EncounterTemplate::whereUserId(Auth::getUser()->id)->get();
        $user = User::find(Auth::getUser()->id);
        //Para preguntas de especialidad de Urología(42)
        $specialities = $user->practitioner?->specialties->pluck('id')->contains(42);

        if ($encounter_sections_user->count() > 0) {
            $this->encounter_sections = EncounterSection::whereIn('id', $encounter_sections_user->pluck('encounter_section_id'))->get();
        } elseif ($specialities && auth()->user()->hasRole('practitioner')) {
            $this->encounter_sections = EncounterSection::whereNull('deleted_at')->get();
        }else{
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
