<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\PediatricGrowthMeasurement;
use App\Services\WHOGrowthCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GrowthTracking extends Component
{
    public $encounter_id;

    public $encounter;

    public $patient_id;

    public $patient;

    public $measurement_date = '';

    public $weight_kg = '';

    public $height_cm = '';

    public $head_circumference_cm = '';

    public $bmi = '';

    public $age_months = 0;

    public $gender = '';

    public $note = '';

    public $measurements = [];

    public $chartData = [];

    public $activeChart = 'weight_for_age';

    public $editingMeasurementId = null;

    public $previewPercentiles = [];

    protected $rules = [
        'measurement_date' => 'required|date',
        'weight_kg' => 'nullable|numeric|min:0|max:200',
        'height_cm' => 'nullable|numeric|min:0|max:250',
        'head_circumference_cm' => 'nullable|numeric|min:0|max:100',
        'note' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'measurement_date.required' => 'La fecha de medición es requerida.',
        'weight_kg.numeric' => 'El peso debe ser un número válido.',
        'height_cm.numeric' => 'La altura debe ser un número válido.',
    ];

    public function mount($encounter_id = null, $patient_id = null)
    {
        if ($encounter_id) {
            $this->encounter = Encounter::find($encounter_id);
            $this->patient = Patient::find($this->encounter->patient_id);
        } elseif ($patient_id) {
            $this->patient = Patient::find($patient_id);
        }

        $this->measurement_date = now()->format('Y-m-d');
        $this->calculateAgeAndGender();
        $this->loadMeasurements();
        $this->prepareChartData();
    }

    public function calculateAgeAndGender()
    {
        if ($this->patient && $this->patient->birth_date) {
            $birthDate = Carbon::parse($this->patient->birth_date);
            $measurementDate = $this->measurement_date ? Carbon::parse($this->measurement_date) : Carbon::now();
            $this->age_months = $birthDate->diffInMonths($measurementDate);

            $patientGender = strtolower($this->patient->gender ?? '');
            if (in_array($patientGender, ['masculino', 'male', 'm'])) {
                $this->gender = 'male';
            } elseif (in_array($patientGender, ['femenino', 'female', 'f'])) {
                $this->gender = 'female';
            }
        }
    }

    public function updatedMeasurementDate()
    {
        $this->calculateAgeAndGender();
        $this->calculateBMIPreview();
    }

    public function updatedWeightKg()
    {
        $this->calculateBMIPreview();
    }

    public function updatedHeightCm()
    {
        $this->calculateBMIPreview();
    }

    public function calculateBMIPreview()
    {
        if ($this->weight_kg && $this->height_cm) {
            $heightM = $this->height_cm / 100;
            if ($heightM > 0) {
                $this->bmi = number_format($this->weight_kg / ($heightM * $heightM), 2);

                $this->calculatePreviewPercentiles();
            }
        } else {
            $this->bmi = '';
            $this->previewPercentiles = [];
        }
    }

    private function calculatePreviewPercentiles()
    {
        if (! $this->gender || ! $this->age_months) {
            return;
        }

        $calculator = new WHOGrowthCalculator;
        $this->previewPercentiles = [];

        if ($this->weight_kg) {
            $result = $calculator->calculate('weight_for_age', $this->gender, $this->age_months, (float) $this->weight_kg);
            $this->previewPercentiles['weight_for_age'] = $result;
        }

        if ($this->height_cm) {
            $result = $calculator->calculate('height_for_age', $this->gender, $this->age_months, (float) $this->height_cm);
            $this->previewPercentiles['height_for_age'] = $result;
        }

        if ($this->bmi) {
            $result = $calculator->calculate('bmi_for_age', $this->gender, $this->age_months, (float) $this->bmi);
            $this->previewPercentiles['bmi_for_age'] = $result;
        }

        if ($this->head_circumference_cm && $this->age_months <= 24) {
            $result = $calculator->calculate('head_circumference_for_age', $this->gender, $this->age_months, (float) $this->head_circumference_cm);
            $this->previewPercentiles['head_circ_for_age'] = $result;
        }
    }

    public function loadMeasurements()
    {
        $this->measurements = PediatricGrowthMeasurement::where('patient_id', $this->patient->id)
            ->orderBy('measurement_date', 'desc')
            ->get();
    }

    public function saveMeasurement()
    {
        $this->validate();

        if (empty($this->weight_kg) && empty($this->height_cm) && empty($this->head_circumference_cm)) {
            $this->dispatch('showToastrGrowth', type: 'error', message: 'Debe ingresar al menos una medición (peso, altura o circunferencia cefálica).');

            return;
        }

        $data = [
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter->id ?? null,
            'practitioner_id' => Auth::user()->practitioner->id ?? 1,
            'client_id' => Auth::user()->client_id ?? null,
            'branch_id' => Auth::user()->branch_id ?? null,
            'consulting_room_id' => $this->encounter->consulting_room_id ?? null,
            'measurement_date' => $this->measurement_date,
            'age_months' => $this->age_months,
            'gender' => $this->gender,
            'weight_kg' => $this->weight_kg ?: null,
            'height_cm' => $this->height_cm ?: null,
            'head_circumference_cm' => $this->head_circumference_cm ?: null,
            'note' => $this->note,
        ];

        if ($this->editingMeasurementId) {
            $measurement = PediatricGrowthMeasurement::find($this->editingMeasurementId);
            $measurement->update($data);
            $this->dispatch('showToastrGrowth', type: 'success', message: 'Medición actualizada correctamente.');
        } else {
            PediatricGrowthMeasurement::create($data);
            $this->dispatch('showToastrGrowth', type: 'success', message: 'Medición registrada correctamente.');
        }

        $this->resetForm();
        $this->loadMeasurements();
        $this->prepareChartData();
    }

    public function editMeasurement($measurementId)
    {
        $measurement = PediatricGrowthMeasurement::find($measurementId);

        if (! $measurement || $measurement->patient_id !== $this->patient->id) {
            $this->dispatch('showToastrGrowth', type: 'error', message: 'Medición no encontrada.');

            return;
        }

        $this->editingMeasurementId = $measurementId;
        $this->measurement_date = $measurement->measurement_date->format('Y-m-d');
        $this->weight_kg = $measurement->weight_kg ?? '';
        $this->height_cm = $measurement->height_cm ?? '';
        $this->head_circumference_cm = $measurement->head_circumference_cm ?? '';
        $this->note = $measurement->note ?? '';

        $this->calculateAgeAndGender();
        $this->calculateBMIPreview();
    }

    public function deleteMeasurement($measurementId)
    {
        $measurement = PediatricGrowthMeasurement::find($measurementId);

        if (! $measurement || $measurement->patient_id !== $this->patient->id) {
            $this->dispatch('showToastrGrowth', type: 'error', message: 'Medición no encontrada.');

            return;
        }

        $measurement->delete();
        $this->dispatch('showToastrGrowth', type: 'success', message: 'Medición eliminada correctamente.');
        $this->loadMeasurements();
        $this->prepareChartData();
    }

    public function setActiveChart($chartType)
    {
        $this->activeChart = $chartType;
        $this->prepareChartData();
    }

    public function prepareChartData()
    {
        if (! $this->gender || $this->measurements->isEmpty()) {
            $this->chartData = [];

            return;
        }

        $calculator = new WHOGrowthCalculator;
        $maxAge = min($this->age_months + 6, 60);

        if ($this->activeChart === 'head_circumference_for_age') {
            $maxAge = min($maxAge, 24);
        }

        $whoCurves = $calculator->getPercentileCurves($this->activeChart, $this->gender, $maxAge);

        $patientData = [
            'ages' => [],
            'values' => [],
        ];

        $measurementField = match ($this->activeChart) {
            'weight_for_age' => 'weight_kg',
            'height_for_age' => 'height_cm',
            'bmi_for_age' => 'bmi',
            'head_circumference_for_age' => 'head_circumference_cm',
            default => 'weight_kg',
        };

        foreach ($this->measurements->sortBy('age_months') as $measurement) {
            if ($measurement->$measurementField && $measurement->age_months <= $maxAge) {
                $patientData['ages'][] = $measurement->age_months;
                $patientData['values'][] = (float) $measurement->$measurementField;
            }
        }

        $this->chartData = [
            'who_curves' => $whoCurves,
            'patient_data' => $patientData,
            'chart_type' => $this->activeChart,
        ];

        $this->dispatch('updateGrowthChart', $this->chartData);
    }

    public function resetForm()
    {
        $this->editingMeasurementId = null;
        $this->measurement_date = now()->format('Y-m-d');
        $this->weight_kg = '';
        $this->height_cm = '';
        $this->head_circumference_cm = '';
        $this->bmi = '';
        $this->note = '';
        $this->previewPercentiles = [];
        $this->calculateAgeAndGender();
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.consultation.growth-tracking');
    }
}
