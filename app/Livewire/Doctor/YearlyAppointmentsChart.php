<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $this->dispatch('loadYearlyAppointmentsChart', $this->chartData);
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

        // Inicializar categorías (meses hasta el mes actual)
        $categories = [];
        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('M');
            $categories[] = $monthName;
        }

        // Obtener todas las sedes con citas del practitioner en el año actual
        $branches = DB::table('appointments')
            ->join('consulting_rooms', 'appointments.consulting_room_id', '=', 'consulting_rooms.id')
            ->join('branches', 'consulting_rooms.branch_id', '=', 'branches.id')
            ->where('appointments.practitioner_id', $practitionerId)
            ->whereYear('appointments.start', $currentYear)
            ->whereMonth('appointments.start', '<=', $currentMonth)
            ->whereNull('appointments.deleted_at')
            ->select('branches.id', 'branches.name')
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('branches.name')
            ->get();

        $series = [];

        foreach ($branches as $branch) {
            // Obtener citas por mes para esta sede
            $monthlyData = Appointment::query()
                ->join('consulting_rooms', 'appointments.consulting_room_id', '=', 'consulting_rooms.id')
                ->where('consulting_rooms.branch_id', $branch->id)
                ->where('appointments.practitioner_id', $practitionerId)
                ->whereYear('appointments.start', $currentYear)
                ->whereMonth('appointments.start', '<=', $currentMonth)
                ->whereNull('appointments.deleted_at')
                ->select(
                    DB::raw('MONTH(appointments.start) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $data = [];
            for ($month = 1; $month <= $currentMonth; $month++) {
                $data[] = $monthlyData->has($month)
                    ? (int) $monthlyData->get($month)->total
                    : 0;
            }

            $series[] = [
                'name' => $branch->name,
                'data' => $data,
            ];
        }

        $this->chartData = [
            'categories' => $categories,
            'series' => $series,
        ];
    }
}
