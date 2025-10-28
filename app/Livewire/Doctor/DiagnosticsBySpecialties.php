<?php

namespace App\Livewire\Doctor;

use App\Models\EncounterDiagnosis;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DiagnosticsBySpecialties extends Component
{

     public $top_specialties;

    public function mount()
    {
        $this->top_specialties = EncounterDiagnosis::with(['condition', 'encounter.appointment.medicalSpecialty'])
    ->selectRaw('
        conditions.onset_info,
        medical_specialties.name as specialty,
        COUNT(encounter_diagnoses.id) as total,
        (COUNT(encounter_diagnoses.id) * 100.0 / specialties_total.total_specialty) as percentage
    ')
    ->join('conditions', 'encounter_diagnoses.condition_id', '=', 'conditions.id')
    ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
    ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
    ->join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id')
    ->joinSub(
        DB::table('encounter_diagnoses')
            ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
            ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
            ->join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id')
            ->selectRaw('medical_specialties.id, COUNT(*) as total_specialty')
            ->groupBy('medical_specialties.id'),
        'specialties_total',
        'medical_specialties.id',
        '=',
        'specialties_total.id'
    )
    ->groupBy('conditions.onset_info', 'medical_specialties.name', 'specialties_total.total_specialty')
    ->limit(5)
    ->get();

    }

    public function render()
    {
        return view('livewire.doctor.diagnostics-by-specialties');
    }
}
