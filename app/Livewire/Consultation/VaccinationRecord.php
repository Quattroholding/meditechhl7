<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Immunization;
use App\Models\Patient;
use App\Models\VaccineType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VaccinationRecord extends Component
{
    public $encounter_id;

    public $encounter;

    public $patient_id;

    public $patient;

    public $showVaccineModal = false;

    public $editingImmunizationId = null;

    public $vaccine_type_id = '';

    public $occurrence_datetime = '';

    public $status = 'completed';

    public $lot_number = '';

    public $expiration_date = '';

    public $manufacturer = '';

    public $route_code = 'IM';

    public $site_code = '';

    public $dose_quantity = 0.5;

    public $dose_unit = 'mL';

    public $dose_number = 1;

    public $series_doses = 1;

    public $note = '';

    public $reaction = '';

    public $vaccinationSchedule = [];

    public $patientAgeMonths = 0;

    protected $rules = [
        'vaccine_type_id' => 'required|exists:vaccine_types,id',
        'occurrence_datetime' => 'required|date',
        'lot_number' => 'nullable|string|max:100',
        'expiration_date' => 'nullable|date|after:occurrence_datetime',
        'route_code' => 'required|in:IM,PO,NASAL,SC,ID',
        'dose_quantity' => 'nullable|numeric|min:0|max:10',
        'dose_number' => 'required|integer|min:1|max:10',
        'series_doses' => 'required|integer|min:1|max:10',
    ];

    protected $messages = [
        'vaccine_type_id.required' => 'Debe seleccionar un tipo de vacuna.',
        'occurrence_datetime.required' => 'La fecha de administración es requerida.',
        'expiration_date.after' => 'La fecha de vencimiento debe ser posterior a la fecha de administración.',
        'dose_number.max' => 'El número de dosis no puede exceder 10.',
    ];

    public function mount($encounter_id = null, $patient_id = null)
    {
        if ($encounter_id) {
            $this->encounter = Encounter::find($encounter_id);
            $this->patient = Patient::find($this->encounter->patient_id);
        } elseif ($patient_id) {
            $this->patient = Patient::find($patient_id);
        }

        $this->calculatePatientAge();
        $this->buildVaccinationSchedule();
        $this->occurrence_datetime = now()->format('Y-m-d H:i');
    }

    public function calculatePatientAge()
    {
        if ($this->patient && $this->patient->birth_date) {
            $birthDate = Carbon::parse($this->patient->birth_date);
            $this->patientAgeMonths = $birthDate->diffInMonths(Carbon::now());
        }
    }

    public function buildVaccinationSchedule()
    {
        $vaccineTypes = VaccineType::active()->ordered()->get();
        $immunizations = Immunization::where('patient_id', $this->patient->id)
            ->with('vaccineType')
            ->get()
            ->groupBy('vaccine_type_id');

        $schedule = [];

        foreach ($vaccineTypes as $vaccineType) {
            $givenDoses = $immunizations->get($vaccineType->id, collect());
            $isAgeAppropriate = $vaccineType->isAgeAppropriate($this->patientAgeMonths);

            // Check if vaccination is complete first (regardless of age)
            if ($givenDoses->count() >= $vaccineType->doses_required) {
                $status = 'complete';
            } elseif ($givenDoses->count() > 0) {
                // Has some doses but not all
                $lastDose = $givenDoses->sortByDesc('occurrence_datetime')->first();
                $daysSinceLastDose = Carbon::parse($lastDose->occurrence_datetime)->diffInDays(Carbon::now());

                if ($vaccineType->interval_days && $daysSinceLastDose >= $vaccineType->interval_days) {
                    $status = 'due';
                } else {
                    $status = 'partial';
                }
            } elseif ($isAgeAppropriate) {
                // No doses and within age range
                $ageInMonths = $this->patientAgeMonths;
                if ($ageInMonths >= $vaccineType->min_age_months) {
                    $status = 'overdue';
                } else {
                    $status = 'pending';
                }
            } else {
                // No doses and outside age range
                $status = 'not_age_appropriate';
            }

            $schedule[] = [
                'vaccine_type' => $vaccineType,
                'given_doses' => $givenDoses->sortBy('dose_number'),
                'status' => $status,
                'is_age_appropriate' => $isAgeAppropriate,
                'next_dose_number' => $givenDoses->count() + 1,
            ];
        }

        $this->vaccinationSchedule = $schedule;
    }

    public function addVaccine($vaccineTypeId)
    {
        $this->resetForm();
        $this->vaccine_type_id = $vaccineTypeId;

        $vaccineType = VaccineType::find($vaccineTypeId);
        $givenDoses = Immunization::where('patient_id', $this->patient->id)
            ->where('vaccine_type_id', $vaccineTypeId)
            ->count();

        $this->dose_number = $givenDoses + 1;
        $this->series_doses = $vaccineType->doses_required;
        $this->showVaccineModal = true;
    }

    public function editImmunization($immunizationId)
    {
        $immunization = Immunization::find($immunizationId);

        if (! $immunization || $immunization->patient_id !== $this->patient->id) {
            $this->dispatch('showToastrVaccination', type: 'error', message: 'Inmunización no encontrada.');

            return;
        }

        $this->editingImmunizationId = $immunizationId;
        $this->vaccine_type_id = $immunization->vaccine_type_id;
        $this->occurrence_datetime = $immunization->occurrence_datetime->format('Y-m-d H:i');
        $this->status = $immunization->status;
        $this->lot_number = $immunization->lot_number ?? '';
        $this->expiration_date = $immunization->expiration_date ? $immunization->expiration_date->format('Y-m-d') : '';
        $this->manufacturer = $immunization->manufacturer ?? '';
        $this->route_code = $immunization->route_code ?? 'IM';
        $this->site_code = $immunization->site_code ?? '';
        $this->dose_quantity = $immunization->dose_quantity ?? 0.5;
        $this->dose_unit = $immunization->dose_unit ?? 'mL';
        $this->dose_number = $immunization->dose_number ?? 1;
        $this->series_doses = $immunization->series_doses ?? 1;
        $this->note = $immunization->note ?? '';
        $this->reaction = is_array($immunization->reaction) && count($immunization->reaction) > 0
            ? $immunization->reaction[0]['detail'] ?? ''
            : '';

        $this->showVaccineModal = true;
    }

    public function saveImmunization()
    {
        $this->validate();

        $vaccineType = VaccineType::find($this->vaccine_type_id);

        if (! $vaccineType) {
            $this->dispatch('showToastrVaccination', type: 'error', message: 'Tipo de vacuna no encontrado.');

            return;
        }

        $reactionData = null;
        if (! empty($this->reaction)) {
            $reactionData = [
                [
                    'date' => now()->format('Y-m-d'),
                    'detail' => $this->reaction,
                ],
            ];
        }

        $data = [
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter->id ?? null,
            'practitioner_id' => Auth::user()->practitioner->id ?? 1,
            'vaccine_type_id' => $this->vaccine_type_id,
            'client_id' => Auth::user()->client_id ?? null,
            'branch_id' => Auth::user()->branch_id ?? null,
            'consulting_room_id' => $this->encounter->consulting_room_id ?? null,
            'vaccine_code' => $vaccineType->cvx_code,
            'vaccine_display' => $vaccineType->localized_name,
            'occurrence_datetime' => $this->occurrence_datetime,
            'status' => $this->status,
            'lot_number' => $this->lot_number,
            'expiration_date' => $this->expiration_date ?: null,
            'manufacturer' => $this->manufacturer,
            'route_code' => $this->route_code,
            'site_code' => $this->site_code,
            'dose_quantity' => $this->dose_quantity,
            'dose_unit' => $this->dose_unit,
            'dose_number' => $this->dose_number,
            'series_doses' => $this->series_doses,
            'reaction' => $reactionData,
            'note' => $this->note,
        ];

        if ($this->editingImmunizationId) {
            $immunization = Immunization::find($this->editingImmunizationId);
            $immunization->update($data);
            $this->dispatch('showToastrVaccination', type: 'success', message: 'Inmunización actualizada correctamente.');
        } else {
            Immunization::create($data);
            $this->dispatch('showToastrVaccination', type: 'success', message: 'Inmunización registrada correctamente.');
        }

        $this->closeModal();
        $this->buildVaccinationSchedule();
    }

    public function deleteImmunization($immunizationId)
    {
        $immunization = Immunization::find($immunizationId);

        if (! $immunization || $immunization->patient_id !== $this->patient->id) {
            $this->dispatch('showToastrVaccination', type: 'error', message: 'Inmunización no encontrada.');

            return;
        }

        $immunization->delete();
        $this->dispatch('showToastrVaccination', type: 'success', message: 'Inmunización eliminada correctamente.');
        $this->buildVaccinationSchedule();
    }

    public function closeModal()
    {
        $this->showVaccineModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingImmunizationId = null;
        $this->vaccine_type_id = '';
        $this->occurrence_datetime = now()->format('Y-m-d H:i');
        $this->status = 'completed';
        $this->lot_number = '';
        $this->expiration_date = '';
        $this->manufacturer = '';
        $this->route_code = 'IM';
        $this->site_code = '';
        $this->dose_quantity = 0.5;
        $this->dose_unit = 'mL';
        $this->dose_number = 1;
        $this->series_doses = 1;
        $this->note = '';
        $this->reaction = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.consultation.vaccination-record');
    }
}
