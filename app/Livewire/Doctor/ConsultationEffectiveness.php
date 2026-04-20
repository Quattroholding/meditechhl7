<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        'fulfilled' => 4,
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
        $days = (int) $this->timeFrame;
        $userId = auth()->id();

        // Cache key único por usuario y timeframe
        $cacheKey = "consultation_effectiveness_user_{$userId}_days_{$days}";

        // Cache por 10 minutos - procesamiento MUY pesado
        $data = Cache::tags(['doctor_dashboard', 'effectiveness', 'appointments'])
            ->remember($cacheKey, 600, function () use ($days) {
                return $this->fetchEffectivenessData($days);
            });

        // Asignar datos desde cache
        $this->totalAppointments = $data['totalAppointments'];
        $this->effectivenessData = $data['effectivenessData'];
        $this->averageCompletionTime = $data['averageCompletionTime'];
        $this->statusCounts = $data['statusCounts'];
        $this->dropoffPoints = $data['dropoffPoints'];
        $this->conversionRate = $data['conversionRate'];
    }

    private function fetchEffectivenessData($days)
    {
        // Obtener citas del período seleccionado
        $appointments = Appointment::query()
            ->when($days > 0, function ($query) use ($days) {
                return $query->where('start', '>=', now()->subDays($days));
            })
            ->get();

        $totalAppointments = $appointments->count();

        if ($totalAppointments == 0) {
            return [
                'totalAppointments' => 0,
                'effectivenessData' => [],
                'averageCompletionTime' => 0,
                'statusCounts' => [],
                'dropoffPoints' => [],
                'conversionRate' => 0,
            ];
        }

        // Obtener historial de estados para estas citas
        $appointmentIds = $appointments->pluck('id');
        $statusHistory = AppointmentStatus::query()
            ->whereIn('appointment_id', $appointmentIds)
            ->orderBy('appointment_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('appointment_id');

        // Calcular todas las métricas
        $effectivenessData = $this->computeEffectivenessMetrics($statusHistory);
        $statusCounts = $this->computeStatusCounts($statusHistory);
        $dropoffPoints = $this->computeDropoffPoints($statusHistory, $totalAppointments);
        $conversionRate = $this->computeConversionRate($statusHistory, $totalAppointments);

        return [
            'totalAppointments' => $totalAppointments,
            'effectivenessData' => $effectivenessData['data'],
            'averageCompletionTime' => $effectivenessData['avgTime'],
            'statusCounts' => $statusCounts,
            'dropoffPoints' => $dropoffPoints,
            'conversionRate' => $conversionRate,
        ];
    }

    private function computeEffectivenessMetrics($statusHistory)
    {
        $completionTimes = [];
        $effectivenessData = [
            'booked_to_arrived' => 0,
            'arrived_to_checked_in' => 0,
            'checked_in_to_fulfilled' => 0,
            'booked_to_fulfilled' => 0,
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $statusesByStatus = $statuses->keyBy('status');

            // Calcular tiempo entre estados
            if (isset($statusesByStatus['booked']) && isset($statusesByStatus['arrived'])) {
                $effectivenessData['booked_to_arrived']++;
                $diff = Carbon::parse($statusesByStatus['arrived']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['booked']->created_at));
                $completionTimes['booked_to_arrived'][] = $diff;
            }

            if (isset($statusesByStatus['arrived']) && isset($statusesByStatus['checked-in'])) {
                $effectivenessData['arrived_to_checked_in']++;
                $diff = Carbon::parse($statusesByStatus['checked-in']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['arrived']->created_at));
                $completionTimes['arrived_to_checked_in'][] = $diff;
            }

            if (isset($statusesByStatus['checked-in']) && isset($statusesByStatus['fulfilled'])) {
                $effectivenessData['checked_in_to_fulfilled']++;
                $diff = Carbon::parse($statusesByStatus['fulfilled']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['checked-in']->created_at));
                $completionTimes['checked_in_to_fulfilled'][] = $diff;
            }

            if (isset($statusesByStatus['booked']) && isset($statusesByStatus['fulfilled'])) {
                $effectivenessData['booked_to_fulfilled']++;
                $diff = Carbon::parse($statusesByStatus['fulfilled']->created_at)
                    ->diffInMinutes(Carbon::parse($statusesByStatus['booked']->created_at));
                $completionTimes['booked_to_fulfilled'][] = $diff;
            }
        }

        // Calcular tiempo promedio de completación total
        $avgTime = 0;
        if (isset($completionTimes['booked_to_fulfilled']) && count($completionTimes['booked_to_fulfilled']) > 0) {
            $avgTime = round(array_sum($completionTimes['booked_to_fulfilled']) / count($completionTimes['booked_to_fulfilled']));
        }

        return ['data' => $effectivenessData, 'avgTime' => $avgTime];
    }

    private function computeStatusCounts($statusHistory)
    {
        $statusCounts = [
            'booked' => 0,
            'arrived' => 0,
            'checked-in' => 0,
            'fulfilled' => 0,
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $maxStatus = $statuses->max('status');

            // Contar cuántas citas llegaron a cada estado
            foreach ($this->expectedStatusFlow as $status => $order) {
                if ($this->getStatusOrder($maxStatus) >= $order) {
                    $statusCounts[$status]++;
                }
            }
        }

        return $statusCounts;
    }

    private function computeDropoffPoints($statusHistory, $totalAppointments)
    {
        $dropoffPoints = [];
        $transitions = [
            'booked_to_arrived' => 0,
            'arrived_to_checked_in' => 0,
            'checked_in_to_fulfilled' => 0,
        ];

        foreach ($statusHistory as $appointmentId => $statuses) {
            $statusesByStatus = $statuses->keyBy('status');

            // Calcular abandono en cada transición
            if (isset($statusesByStatus['booked']) && ! isset($statusesByStatus['arrived'])) {
                $transitions['booked_to_arrived']++;
            }
            if (isset($statusesByStatus['arrived']) && ! isset($statusesByStatus['checked-in'])) {
                $transitions['arrived_to_checked_in']++;
            }
            if (isset($statusesByStatus['checked-in']) && ! isset($statusesByStatus['fulfilled'])) {
                $transitions['checked_in_to_fulfilled']++;
            }
        }

        foreach ($transitions as $transition => $dropoffs) {
            $dropoffPoints[$transition] = [
                'count' => $dropoffs,
                'percentage' => $totalAppointments > 0 ? round(($dropoffs / $totalAppointments) * 100, 1) : 0,
            ];
        }

        return $dropoffPoints;
    }

    private function computeConversionRate($statusHistory, $totalAppointments)
    {
        $fulfilledCount = 0;

        foreach ($statusHistory as $appointmentId => $statuses) {
            if ($statuses->where('status', 'fulfilled')->isNotEmpty()) {
                $fulfilledCount++;
            }
        }

        return $totalAppointments > 0 ?
            round(($fulfilledCount / $totalAppointments) * 100, 1) : 0;
    }

    private function getStatusOrder($status)
    {
        return $this->expectedStatusFlow[$status] ?? 0;
    }

    public function getEffectivenessPercentage($fromStatus, $toStatus)
    {
        $key = $fromStatus.'_to_'.str_replace('-', '_', $toStatus);
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
            return $minutes.' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours.'h '.$remainingMinutes.'m';
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
