<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\ClinicalObservationType;
use App\Models\Patient;
use Carbon\Carbon;
use Livewire\Component;

class VitalSignsStatus extends Component
{
    public $patientId;

    public $patient;

    public $selectedPeriod = 7; // días

    public $autoRefresh = true;

    public $isLoading = true;

    public $order;

    public array $vitalSignsConfig = [];

    public function mount($patientId = null)
    {
        $this->patientId = $patientId ?? 1; // Default para demo
        $this->loadPatient();
        foreach (ClinicalObservationType::whereCategory('vital_sign')->whereNotIn('code', ['29463-7', '8302-2'])->get() as $ot) {
            $this->vitalSignsConfig[$ot->name] = [
                'name' => $ot->name,
                'loinc_code' => $ot->code, // Blood pressure panel
                'icon' => $ot->icon,
                'unit' => $ot->unit,
                'normal_range' => [$ot->min_normal_value, $ot->max_normal_value],
                'priority' => $ot->priority,
            ];
        }
    }

    public function loadData()
    {
        $this->loadPatient();
        $this->isLoading = false;
    }

    public function loadPatient()
    {
        $this->patient = Patient::find($this->patientId);
    }

    public function updatedSelectedPeriod()
    {
        // Se actualizará automáticamente en el siguiente render
    }

    public function refreshData()
    {
        $this->loadPatient();
        $this->dispatch('vital-signs-refreshed');
    }

    public function getLatestVitalSignsProperty()
    {
        if (! $this->patient) {
            return collect();
        }

        $vitalSigns = collect();

        foreach ($this->vitalSignsConfig as $key => $config) {
            $observation = $this->patient->vitalSigns()
                ->whereCode($config['loinc_code'])
                ->where('effective_date', '>=', Carbon::now()->subDays($this->selectedPeriod))
                ->latest()
                ->first();

            if ($observation) {

                $vitalSign = [
                    'key' => $key,
                    'config' => $config,
                    'observation' => $observation,
                    'value' => $this->formatVitalSignValue($observation, $config),
                    'status' => $this->getVitalSignStatus($observation, $config),
                    'risk_level' => $this->calculateRiskLevel($observation, $config),
                    'trend' => $this->calculateTrend($key, $config['loinc_code']),
                    'last_updated' => $observation->effective_date,
                ];

                $vitalSigns->push($vitalSign);
            }
        }

        return $vitalSigns->sortBy(function ($item) {
            return $item['config']['priority'];
        });
    }

    public function getOverallHealthStatusProperty()
    {
        $vitalSigns = $this->latestVitalSigns;

        if ($vitalSigns->isEmpty()) {
            return [
                'status' => 'unknown',
                'message' => 'No hay datos de signos vitales disponibles',
                'color' => 'gray',
                'score' => 0,
            ];
        }

        $riskLevels = $vitalSigns->pluck('risk_level');
        $criticalCount = $riskLevels->filter(fn ($level) => $level === 'critical')->count();
        $highCount = $riskLevels->filter(fn ($level) => $level === 'high')->count();
        $abnormalCount = $riskLevels->filter(fn ($level) => in_array($level, ['high', 'low']))->count();
        $normalCount = $riskLevels->filter(fn ($level) => $level === 'normal')->count();

        $totalSigns = $vitalSigns->count();
        $score = ($normalCount / $totalSigns) * 100;

        if ($criticalCount > 0) {
            return [
                'status' => 'critical',
                'message' => 'Requiere atención médica inmediata',
                'color' => 'red',
                'score' => $score,
            ];
        }

        if ($highCount > 1 || $abnormalCount > ($totalSigns / 2)) {
            return [
                'status' => 'warning',
                'message' => 'Algunos signos vitales requieren atención',
                'color' => 'yellow',
                'score' => $score,
            ];
        }

        if ($abnormalCount > 0) {
            return [
                'status' => 'caution',
                'message' => 'Monitoreo recomendado',
                'color' => 'orange',
                'score' => $score,
            ];
        }

        return [
            'status' => 'normal',
            'message' => 'Signos vitales dentro de rango normal',
            'color' => 'green',
            'score' => $score,
        ];
    }

    private function formatVitalSignValue($observation, $config)
    {
        // Para presión arterial (tiene componentes)
        if ($config['loinc_code'] === '85354-9') {
            $components = $observation->components;
            $systolic = collect($components)->firstWhere('display', 'Systolic blood pressure');
            $diastolic = collect($components)->firstWhere('display', 'Diastolic blood pressure');

            if ($systolic && $diastolic) {
                return [
                    'display' => "{$systolic['formatted']}/{$diastolic['formatted']}",
                    'systolic' => $systolic['value'],
                    'diastolic' => $diastolic['value'],
                ];
            }
        }

        // Para otros signos vitales simples
        return [
            'display' => $observation->formatted_value,
            'numeric' => $observation->numeric_value,
        ];
    }

    private function getVitalSignStatus($observation, $config)
    {
        $riskLevel = $this->calculateRiskLevel($observation, $config);

        switch ($riskLevel) {
            case 'critical':
                return 'Crítico';
            case 'high':
                return 'Alto';
            case 'low':
                return 'Bajo';
            case 'normal':
                return 'Normal';
            default:
                return 'Sin datos';
        }
    }

    private function calculateRiskLevel($observation, $config)
    {

        // Para presión arterial

        if ($config['loinc_code'] === '85354-9') {
            $components = $observation->components;
            dd($components);
            $systolic = collect($components)->firstWhere('display', 'Systolic blood pressure');
            $diastolic = collect($components)->firstWhere('display', 'Diastolic blood pressure');

            if ($systolic && $diastolic) {
                $sysValue = $systolic['value'];
                $diaValue = $diastolic['value'];

                // Hipertensión severa
                if ($sysValue >= 180 || $diaValue >= 110) {
                    return 'critical';
                }
                // Hipertensión
                if ($sysValue >= 140 || $diaValue >= 90) {
                    return 'high';
                }
                // Hipotensión
                if ($sysValue < 90 || $diaValue < 60) {
                    return 'low';
                }

                return 'normal';
            }
        }

        // Para otros signos vitales
        $value = $observation->numeric_value;
        $normalRange = $config['normal_range'];

        if (! $value || ! $normalRange) {
            return 'unknown';
        }

        // Casos específicos por tipo de signo vital
        switch ($config['loinc_code']) {
            case '8867-4': // Frecuencia cardíaca
                if ($value > 120 || $value < 50) {
                    return 'critical';
                }
                if ($value > 100 || $value < 60) {
                    return 'abnormal';
                }
                break;

            case '8310-5': // Temperatura
                if ($value > 39.0 || $value < 35.0) {
                    return 'critical';
                }
                if ($value > 37.5 || $value < 36.0) {
                    return 'abnormal';
                }
                break;

            case '2708-6': // Saturación O2
                if ($value < 90) {
                    return 'critical';
                }
                if ($value < 95) {
                    return 'low';
                }
                break;

            case '9279-1': // Frecuencia respiratoria
                if ($value > 25 || $value < 10) {
                    return 'critical';
                }
                if ($value > 20 || $value < 12) {
                    return 'abnormal';
                }
                break;
        }
        /*
        // Rango normal genérico
        if (is_array($normalRange)) {

            [$min, $max] = $normalRange;
            if ($value < $min) return 'low';
            if ($value > $max) return 'high';
        }
        */

        return 'normal';
    }

    private function calculateTrend($key, $loincCode)
    {
        if (! $this->patient) {
            return 'stable';
        }

        $observations = $this->patient->vitalSigns()
            ->where('effective_date', '>=', Carbon::now()->subDays(30))
            ->latest()
            ->limit(3)
            ->get();

        if ($observations->count() < 2) {
            return 'stable';
        }

        $latest = $observations->first()->numeric_value;
        $previous = $observations->skip(1)->first()->numeric_value;

        if (! $latest || ! $previous) {
            return 'stable';
        }

        $difference = $latest - $previous;
        $percentChange = abs($difference / $previous) * 100;

        if ($percentChange < 5) {
            return 'stable';
        }

        return $difference > 0 ? 'increasing' : 'decreasing';
    }

    public function render()
    {
        return view('livewire.patient.dashboard.vital-signs-status', [
            'vitalSigns' => $this->latestVitalSigns,
            'overallStatus' => $this->overallHealthStatus,
        ]);
    }
}
