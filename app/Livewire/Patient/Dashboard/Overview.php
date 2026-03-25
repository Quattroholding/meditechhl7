<?php

namespace App\Livewire\Patient\Dashboard;

use App\Models\Appointment;
use App\Models\Condition;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\VitalSign;
use Livewire\Component;

class Overview extends Component
{
    public $patient;

    public $order;

    public $isLoading = true;

    public $stats = [];

    public $vitalSigns = null;

    public $nextAppointment = null;

    protected $listeners = ['loadData'];

    public function mount($order = null)
    {
        $this->patient = auth()->user()->patient;
        $this->order = $order;
        // Initialize empty data to avoid errors during loading
        $this->stats = [];
    }

    public function loadData()
    {
        $this->loadStats();
        $this->loadVitalSigns();
        $this->loadNextAppointment();
        $this->isLoading = false;
    }

    public function loadStats()
    {
        if (! $this->patient) {
            $this->stats = [];

            return;
        }

        $this->stats = [
            'appointments' => [
                'total' => Appointment::where('patient_id', $this->patient->id)->count(),
                'upcoming' => Appointment::where('patient_id', $this->patient->id)
                    ->whereDate('start', '>=', now())
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

    public function loadVitalSigns()
    {
        if (! $this->patient) {
            $this->vitalSigns = null;

            return;
        }

        // Get most recent vital signs with observation types
        $vitalSigns = VitalSign::where('patient_id', $this->patient->id)
            ->with('observationType')
            ->orderBy('effective_date', 'desc')
            ->limit(4)
            ->get();

        $this->vitalSigns = $vitalSigns->isNotEmpty() ? $vitalSigns : null;
    }

    public function loadNextAppointment()
    {
        if (! $this->patient) {
            $this->nextAppointment = null;

            return;
        }

        $this->nextAppointment = Appointment::where('patient_id', $this->patient->id)
            ->whereDate('start', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start')
            ->with(['practitioner'])
            ->first();
    }

    public function openModal($date = null, $time = null, $modalTitle = 'Nueva Cita')
    {
        $this->dispatch('openAppointmentModal', 'Nueva Cita');
    }

    public function render()
    {
        return view('livewire.patient.dashboard.overview');
    }
}
