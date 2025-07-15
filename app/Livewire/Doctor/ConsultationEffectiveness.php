<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConsultationEffectiveness extends Component
{
    public $timeFrame = '30'; // Por defecto últimos 30 días
    public $effectivenessData = [];
    public $averageCompletionTime = 0;
    public $totalAppointments = 0;
    public $conversionRate = 0;
    public $statusCounts = [];
    public $dropoffPoints = [];
    public $order;
    public $isLoading = true;

    // Flujo de estados esperado
    private $expectedStatusFlow = [
        'booked' => 1,
        'arrived' => 2,
        'checked-in' => 3,
        'fulfilled' => 4
    ];

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->effectivenessData = [];
        $this->averageCompletionTime = 0;
        $this->totalAppointments = 0;
        $this->conversionRate = 0;
        $this->statusCounts = [];
        $this->dropoffPoints = [];
    }

    public function loadData()
    {
        $this->loadEffectivenessData();
        $this->isLoading = false;
    }

    public function updatedTimeFrame()
    {
        $this->loadEffectivenessData();
    }

    public function loadEffectivenessData()
    {
        $practitionerId = auth()->user()->practitioner->id;
        $days = (int) $this->timeFrame;

        // Obtener citas del período seleccionado
        $appointments = Appointment::query()
            ->where('practitioner_id', $practitionerId)
            ->when($days > 0, function($query) use ($days) {
                return $query->where('start', '>=', now()->subDays($days));
            })
            ->get();

        $this->totalAppointments = $appointments->count();

        if ($this->totalAppointments == 0) {
            $this->resetData();
            return;
        }

        // Obtener historial de estados para estas citas
        $appointmentIds = $appointments->pluck('id');
        $statusHistory = AppointmentStatus::query()
            ->whereIn('appointment_id', $appointmentIds)
            ->orderBy('appointment_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('appointment_id');

        $this->calculateEffectivenessMetrics($statusHistory);
        $this->calculateStatusCounts($statusHistory);
        $this->calculateDropoffPoints($statusHistory);
        $this->calculateConversionRate($statusHistory);
    }

    private function calculateEffectivenessMetrics($statusHistory)
    {
        $completionTimes = [];
        $this->effectivenessData = [
            'booked_to_arrived' => 0,
            'arrived_to_checked_in' => 0,
            'checked_in_to_fulfilled' => 0,
            'booked_to_fulfilled' => 0
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $statusesByStatus = $statuses->keyBy('status');

            // Calcular tiempo entre estados
            if (isset($statusesByStatus['booked']) && isset($statusesByStatus['arrived'])) {
                $this->effectivenessData['booked_to_arrived']++;
                $diff = Carbon::parse($statusesByStatus['arrived']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['booked']->created_at));
                $completionTimes['booked_to_arrived'][] = $diff;
            }

            if (isset($statusesByStatus['arrived']) && isset($statusesByStatus['checked-in'])) {
                $this->effectivenessData['arrived_to_checked_in']++;
                $diff = Carbon::parse($statusesByStatus['checked-in']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['arrived']->created_at));
                $completionTimes['arrived_to_checked_in'][] = $diff;
            }

            if (isset($statusesByStatus['checked-in']) && isset($statusesByStatus['fulfilled'])) {
                $this->effectivenessData['checked_in_to_fulfilled']++;
                $diff = Carbon::parse($statusesByStatus['fulfilled']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['checked-in']->created_at));
                $completionTimes['checked_in_to_fulfilled'][] = $diff;
            }

            if (isset($statusesByStatus['booked']) && isset($statusesByStatus['fulfilled'])) {
                $this->effectivenessData['booked_to_fulfilled']++;
                $diff = Carbon::parse($statusesByStatus['fulfilled']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['booked']->created_at));
                $completionTimes['booked_to_fulfilled'][] = $diff;
            }
        }

        // Calcular tiempo promedio de completación total
        if (isset($completionTimes['booked_to_fulfilled']) && count($completionTimes['booked_to_fulfilled']) > 0) {
            $this->averageCompletionTime = round(array_sum($completionTimes['booked_to_fulfilled']) / count($completionTimes['booked_to_fulfilled']));
        }
    }

    private function calculateStatusCounts($statusHistory)
    {
        $this->statusCounts = [
            'booked' => 0,
            'arrived' => 0,
            'checked-in' => 0,
            'fulfilled' => 0
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $maxStatus = $statuses->max('status');

            // Contar cuántas citas llegaron a cada estado
            foreach ($this->expectedStatusFlow as $status => $order) {
                if ($this->getStatusOrder($maxStatus) >= $order) {
                    $this->statusCounts[$status]++;
                }
            }
        }
    }

    private function calculateDropoffPoints($statusHistory)
    {
        $this->dropoffPoints = [];
        $transitions = [
            'booked_to_arrived' => 0,
            'arrived_to_checked_in' => 0,
            'checked_in_to_fulfilled' => 0
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $statusesByStatus = $statuses->keyBy('status');

            // Calcular abandono en cada transición
            if (isset($statusesByStatus['booked']) && !isset($statusesByStatus['arrived'])) {
                $transitions['booked_to_arrived']++;
            }
            if (isset($statusesByStatus['arrived']) && !isset($statusesByStatus['checked-in'])) {
                $transitions['arrived_to_checked_in']++;
            }
            if (isset($statusesByStatus['checked-in']) && !isset($statusesByStatus['fulfilled'])) {
                $transitions['checked_in_to_fulfilled']++;
            }
        }

        foreach ($transitions as $transition => $dropoffs) {
            $this->dropoffPoints[$transition] = [
                'count' => $dropoffs,
                'percentage' => $this->totalAppointments > 0 ? round(($dropoffs / $this->totalAppointments) * 100, 1) : 0
            ];
        }
    }

    private function calculateConversionRate($statusHistory)
    {
        $fulfilledCount = 0;

        foreach ($statusHistory as $appointmentId => $statuses) {
            if ($statuses->where('status', 'fulfilled')->isNotEmpty()) {
                $fulfilledCount++;
            }
        }

        $this->conversionRate = $this->totalAppointments > 0 ?
            round(($fulfilledCount / $this->totalAppointments) * 100, 1) : 0;
    }

    private function getStatusOrder($status)
    {
        return $this->expectedStatusFlow[$status] ?? 0;
    }

    public function getEffectivenessPercentage($fromStatus, $toStatus)
    {
        $key = $fromStatus . '_to_' . str_replace('-', '_', $toStatus);
        $count = $this->effectivenessData[$key] ?? 0;

        return $this->totalAppointments > 0 ?
            round(($count / $this->totalAppointments) * 100, 1) : 0;
    }

    public function getStatusPercentage($status)
    {
        $count = $this->statusCounts[$status] ?? 0;
        return $this->totalAppointments > 0 ?
            round(($count / $this->totalAppointments) * 100, 1) : 0;
    }

    public function getFormattedTime($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours . 'h ' . $remainingMinutes . 'm';
    }

    private function resetData()
    {
        $this->effectivenessData = [];
        $this->averageCompletionTime = 0;
        $this->conversionRate = 0;
        $this->statusCounts = [];
        $this->dropoffPoints = [];
    }

    public function render()
    {
        return view('livewire.doctor.consultation-effectiveness');
    }
}
