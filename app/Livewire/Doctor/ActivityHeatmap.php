<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ActivityHeatmap extends Component
{
    public $timeFrame = '30'; // Por defecto últimos 30 días
    public $heatmapData = [];
    public $peakHours = [];
    public $peakDays = [];
    public $totalAppointments = 0;
    public $order;
    public $isLoading = true;

    // Días de la semana en español
    private $daysOfWeek = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];

    // Horarios de trabajo (8 AM - 8 PM)
    public $workingHours = [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->heatmapData = [];
        $this->peakHours = [];
        $this->peakDays = [];
        $this->totalAppointments = 0;
    }

    public function loadData()
    {
        $this->loadActivityData();
        $this->isLoading = false;
    }

    public function updatedTimeFrame()
    {
        $this->loadActivityData();
    }

    public function loadActivityData()
    {
        $practitionerId = auth()->user()->practitioner->id;
        $days = (int) $this->timeFrame;

        // Obtener citas confirmadas y finalizadas
        $appointments = Appointment::query()
            ->where('practitioner_id', $practitionerId)
            ->whereIn('status', ['booked', 'arrived', 'checked-in', 'fulfilled'])
            ->when($days > 0, function($query) use ($days) {
                return $query->where('start', '>=', now()->subDays($days));
            })
            ->get();

        $this->totalAppointments = $appointments->count();

        // Inicializar matriz de heatmap
        $this->heatmapData = [];
        foreach ($this->workingHours as $hour) {
            for ($day = 0; $day < 7; $day++) {
                $this->heatmapData[$hour][$day] = 0;
            }
        }

        // Procesar citas para crear el heatmap
        foreach ($appointments as $appointment) {
            $appointmentDate = Carbon::parse($appointment->start);
            $dayOfWeek = $appointmentDate->dayOfWeek;
            $hour = $appointmentDate->hour;

            if (in_array($hour, $this->workingHours)) {
                $this->heatmapData[$hour][$dayOfWeek]++;
            }
        }

        // Calcular horas pico
        $this->calculatePeakHours();

        // Calcular días pico
        $this->calculatePeakDays();
    }

    private function calculatePeakHours()
    {
        $hourlyTotals = [];

        foreach ($this->workingHours as $hour) {
            $hourlyTotals[$hour] = array_sum($this->heatmapData[$hour]);
        }

        arsort($hourlyTotals);

        $this->peakHours = array_slice($hourlyTotals, 0, 3, true);
    }

    private function calculatePeakDays()
    {
        $dailyTotals = [];

        for ($day = 0; $day < 7; $day++) {
            $dailyTotals[$day] = 0;
            foreach ($this->workingHours as $hour) {
                $dailyTotals[$day] += $this->heatmapData[$hour][$day];
            }
        }

        arsort($dailyTotals);

        $this->peakDays = array_slice($dailyTotals, 0, 3, true);
    }

    public function getIntensityClass($value)
    {
        if ($this->totalAppointments == 0) return 'intensity-0';

        $maxValue = max(array_map('max', $this->heatmapData));
        if ($maxValue == 0) return 'intensity-0';

        $percentage = ($value / $maxValue) * 100;

        if ($percentage >= 80) return 'intensity-5';
        if ($percentage >= 60) return 'intensity-4';
        if ($percentage >= 40) return 'intensity-3';
        if ($percentage >= 20) return 'intensity-2';
        if ($percentage > 0) return 'intensity-1';

        return 'intensity-0';
    }

    public function getFormattedHour($hour)
    {
        return $hour < 12 ? $hour . ':00 AM' :
               ($hour == 12 ? '12:00 PM' : ($hour - 12) . ':00 PM');
    }

    public function getDayName($dayIndex)
    {
        return $this->daysOfWeek[$dayIndex];
    }

    public function render()
    {
        return view('livewire.doctor.activity-heatmap');
    }
}
