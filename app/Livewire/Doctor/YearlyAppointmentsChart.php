<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class YearlyAppointmentsChart extends Component
{
    public $chartData = [];

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar sin cargar datos
        $this->chartData = [];
    }

    public function loadData()
    {
        $this->getYearlyAppointments();
        $this->isLoading = false;

        // Emitir evento para renderizar el chart
        $this->dispatch('loadYearlyAppointmentsChart', [
            'categories' => $this->chartData['categories'] ?? [],
            'data' => $this->chartData['data'] ?? [],
        ]);
    }

    public function render()
    {
        return view('livewire.doctor.yearly-appointments-chart');
    }

    private function getYearlyAppointments()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $practitionerId = auth()->user()->practitioner->id;

        // Inicializar array con todos los meses del año hasta el mes actual
        $monthlyData = [];
        $categories = [];

        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('M');
            $categories[] = $monthName;
            $monthlyData[$month] = 0;
        }

        // Obtener citas del practitioner para el año actual hasta el mes actual
        $appointments = Appointment::selectRaw('MONTH(start) as month, COUNT(*) as count')
            ->where('practitioner_id', $practitionerId)
            ->whereYear('start', $currentYear)
            ->whereMonth('start', '<=', $currentMonth)
            ->groupBy('month')
            ->get();

        // Llenar los datos reales
        foreach ($appointments as $appointment) {
            $monthlyData[$appointment->month] = $appointment->count;
        }

        // Convertir a formato para el chart
        $this->chartData = [
            'categories' => $categories,
            'data' => array_values($monthlyData),
        ];
    }
}
