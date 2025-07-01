<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Appointment;
use App\Models\Condition;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\MedicalHistory;
use Livewire\Component;

class Overview extends Component
{
    public $patient;

    public function mount()
    {
        $this->patient = auth()->user()->patient;
    }

    public function getStatsProperty()
    {
        if (!$this->patient) {
            return [];
        }

        return [
            'appointments' => [
                'total' => Appointment::where('patient_id', $this->patient->id)->count(),
                'upcoming' => Appointment::where('patient_id', $this->patient->id)
                    ->where('start', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'completed' => Appointment::where('patient_id', $this->patient->id)
                    ->where('status', 'fulfilled')
                    ->count(),
            ],
            'consultations' => [
                'total' => Encounter::where('patient_id', $this->patient->id)->count(),
                'recent' => Encounter::where('patient_id', $this->patient->id)
                    ->where('created_at', '>=', now()->subMonths(3))
                    ->count(),
            ],
            'invoices' => [
                'total' => Invoice::where('patient_id', $this->patient->id)->count(),
                'outstanding' => Invoice::where('patient_id', $this->patient->id)
                    ->whereIn('payment_status', ['unpaid', 'partial'])
                    ->count(),
                'total_debt' => Invoice::where('patient_id', $this->patient->id)
                    ->whereIn('payment_status', ['unpaid', 'partial'])
                    ->sum('amount_due') ?? 0,
            ],
            'medical_conditions' => [
                'active' => Condition::where('patient_id', $this->patient->id)
                    ->where('clinical_status', 'active')
                    ->count(),
            ],
        ];
    }

    public function getVitalSignsProperty()
    {
        if (!$this->patient) {
            return null;
        }

        // Get most recent vital signs with observation types
        $vitalSigns = \App\Models\VitalSign::where('patient_id', $this->patient->id)
            ->with('observationType')
            ->orderBy('effective_date', 'desc')
            ->limit(4)
            ->get();

        return $vitalSigns->isNotEmpty() ? $vitalSigns : null;
    }

    public function getNextAppointmentProperty()
    {
        if (!$this->patient) {
            return null;
        }

        return Appointment::where('patient_id', $this->patient->id)
            ->where('start', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start')
            ->with(['practitioner'])
            ->first();
    }

    public function render()
    {
        return view('livewire.patient.dashboard.overview');
    }
}
