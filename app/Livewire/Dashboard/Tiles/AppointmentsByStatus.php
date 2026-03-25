<?php

namespace App\Livewire\Dashboard\Tiles;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentsByStatus extends Component
{
    public $pending;

    public $booked;

    public $confirmed;

    public $arrived;

    public $fulfilled;

    public $cancelled;

    public $total;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $today = Carbon::today();

        $this->pending = Appointment::where('status', 'pending')
            ->whereDate('start', $today)
            ->count();
        $this->booked = Appointment::where('status', 'booked')
            ->whereDate('start', $today)
            ->count();
        $this->confirmed = Appointment::where('status', 'confirmed')
            ->whereDate('start', $today)
            ->count();
        $this->arrived = Appointment::where('status', 'arrived')
            ->whereDate('start', $today)
            ->count();
        $this->fulfilled = Appointment::where('status', 'fulfilled')
            ->whereDate('start', $today)
            ->count();
        $this->cancelled = Appointment::where('status', 'cancelled')
            ->whereDate('start', $today)
            ->count();
        $this->total = Appointment::whereDate('start', $today)->count();
    }

    public function render()
    {
        return view('livewire.dashboard.tiles.appointments-by-status');
    }
}
