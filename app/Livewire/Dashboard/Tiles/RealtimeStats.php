<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Encounter;
use Carbon\Carbon;
use Livewire\Component;

class RealtimeStats extends Component
{
    public $appointments_today;
    public $new_patients_today;
    public $active_encounters;
    public $pending_appointments;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $today = Carbon::today();

        $this->appointments_today = Appointment::whereDate('start', $today)->count();
        $this->new_patients_today = Patient::whereDate('created_at', $today)->count();
        $this->active_encounters = Encounter::whereNull('end')
            ->whereDate('start', '>=', $today)
            ->count();
        $this->pending_appointments = Appointment::where('status', 'pending')
            ->whereDate('start', '>=', $today)
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.realtime-stats');
    }
}
