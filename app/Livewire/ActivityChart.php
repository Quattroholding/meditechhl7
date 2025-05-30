<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use Carbon\Carbon;

class ActivityChart extends Component
{
    public $selectedOption1 = 'This Week';

    protected $listeners = ['updateChartData' => 'render'];

    public function render()
    {
        $data = $this->getAppointmentData();
        //dd($data);
        $this->dispatch('updateChart', [
            'lowData' => $data['lowData'],
            'highData' => $data['highData'],
            'categories' => $data['categories'],
        ]);

        return view('livewire.activity-chart', [
            'lowData' => $data['lowData'],
            'highData' => $data['highData'],
            'categories' => $data['categories'],
        ]);
    }

    private function getAppointmentData()
    {
        $now = Carbon::now();
        $lowData = [];
        $highData = [];
        $categories = [];

        switch ($this->selectedOption1) {
            case 'This Week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $categories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                break;
            case 'Last Week':
                $startDate = $now->copy()->subWeek()->startOfWeek();
                $endDate = $now->copy()->subWeek()->endOfWeek();
                $categories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                break;
            case 'This Month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $categories = $this->getDaysInMonth($now->month, $now->year);
                break;
            case 'Last Month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                $categories = $this->getDaysInMonth($now->copy()->subMonth()->month, $now->copy()->subMonth()->year);
                break;
        }

        $appointments = Appointment::wherePractitionerId(auth()->user()->id)->where('status', 'fulfilled')
            ->whereBetween('start', [$startDate, $endDate])
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start)->format('Y-m-d');
            });
        //dd($appointments, $categories);
           foreach ($categories as $index => $category) {
        if ($this->selectedOption1 === 'This Week' || $this->selectedOption1 === 'Last Week') {
            $date = $now->copy()->startOfWeek()->addDays($index)->format('Y-m-d');
        } else {
            $date = Carbon::parse($category)->format('Y-m-d');
        }
            $format_date=Carbon::parse($date)->format('Y-m-d');
            $lowData[] = $appointments->has($format_date) ? $appointments[$format_date]->count() : 0;
            $highData[] = 0; // Ajusta esto según tus necesidades para "High"
        }
        //dd($lowData, $this->selectedOption1);
        //dd($lowData, $highData, $categories, $appointments, $this->selectedOption1);
        return [
            'lowData' => array_values($lowData),
            'highData' => array_values($highData),
            'categories' => array_values($categories)
        ];
    }

    private function getDaysInMonth($month, $year)
    {
        $days = [];
        for ($i = 1; $i <= Carbon::create($year, $month)->daysInMonth; $i++) {
            $days[] = Carbon::create($year, $month, $i)->format('Y-m-d');
        }
        return $days;
    }
}
