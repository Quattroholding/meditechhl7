<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentNotFulfilled extends Component
{
    public $appointmentsNotFulfilled;

    public $percentageChange;

    public $statusClass;

    public $icon;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->appointmentsNotFulfilled = 0;
        $this->percentageChange = 0;
        $this->statusClass = 'status-green';
        $this->icon = 'sort-icon-01.svg';
    }

    public function loadData()
    {
        $this->getAppointmentsNotFulfilled();
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.doctor.appointment-not-fulfilled');
    }

    public function getAppointmentsNotFulfilled()
    {
        $user = auth()->user();
        $isPractitioner = $user->practitioner !== null;
        $isAssistance = $user->hasRole('asistente medico');
        $clientId = $user->default_client_id;

        // Estados activos que no han sido completados
        $activeStatuses = ['proposed', 'pending', 'booked', 'confirm', 'arrived','checked-in'];

        // Query base para citas sin completar
        $appointmentsQuery = Appointment::query()
            ->whereIn('status', $activeStatuses);

        // Filtrar según tipo de usuario
        if ($isAssistance) {
            $appointmentsQuery->where('assisted_by', $user->id);
        } elseif ($isPractitioner) {
            $appointmentsQuery->where('practitioner_id', $user->practitioner->id);
        } else {
            // Para recepcionistas, filtrar por practitioners del mismo cliente
            $appointmentsQuery->whereHas('practitioner', function ($q) use ($clientId) {
                $q->whereHas('user', function ($q2) use ($clientId) {
                    $q2->where('default_client_id', $clientId);
                });
            });
        }

        $this->appointmentsNotFulfilled = $appointmentsQuery->count();

        // Comparar con el mes anterior
        $lastMonth = Carbon::now()->subMonthNoOverflow()->month;
        $lastYear = Carbon::now()->subMonthNoOverflow()->year;

        // Query para citas del mes anterior
        $lastMonthQuery = Appointment::query()
            ->whereIn('status', $activeStatuses)
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear);

        // Aplicar mismo filtro según tipo de usuario
        if ($isAssistance) {
            $lastMonthQuery->where('assisted_by', $user->id);
        } elseif ($isPractitioner) {
            $lastMonthQuery->where('practitioner_id', $user->practitioner->id);
        } else {
            // Para recepcionistas, filtrar por practitioners del mismo cliente
            $lastMonthQuery->whereHas('practitioner', function ($q) use ($clientId) {
                $q->whereHas('user', function ($q2) use ($clientId) {
                    $q2->where('default_client_id', $clientId);
                });
            });
        }

        $lastMonthAppointments = $lastMonthQuery->count();

        // Calcular porcentaje de cambio
        if ($lastMonthAppointments > 0) {
            $this->percentageChange = (($this->appointmentsNotFulfilled - $lastMonthAppointments) / $lastMonthAppointments) * 100;
            if ($this->percentageChange == 0) {
                $this->percentageChange = 100;
            }
        } else {
            $this->percentageChange = $this->appointmentsNotFulfilled > 0 ? 100 : 0;
        }

        // Asignar icon y class según el porcentaje (menos citas sin completar es mejor)
        $this->statusClass = $this->percentageChange <= 0 ? 'status-green' : 'status-pink';
        $this->icon = $this->percentageChange <= 0 ? 'sort-icon-01.svg' : 'sort-icon-02.svg';
    }
}
