<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Counter extends Component
{
    public $function;

    public $count;

    public $icon;

    public $class;

    public $title;

    public $change;

    public $arrowClass;

    public $symbol;

    public function render()
    {
        if ($this->function == 'appointments') {
            $this->appointments();
        }
        if ($this->function == 'patients') {
            $this->patients();
        }
        if ($this->function == 'encounters') {
            $this->encounters();
        }
        if ($this->function == 'invoices') {
            $this->invoices();
        }

        return view('livewire.dashboard.counter');
    }

    public function appointments()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/calendar.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('appointment.titles');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $curr_month = Carbon::now();
        $cacheKey = 'dashboard_appointments_'.auth()->user()->client_id.'_'.$curr_month->format('Y-m');

        $data = CacheHelper::remember(['dashboard', 'appointments'], $cacheKey, 300, function () use ($curr_month) {
            $count = Appointment::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
            $lastMonth = Appointment::whereRaw("start>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

            return ['count' => $count, 'lastMonth' => $lastMonth];
        });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        $this->change = ($this->count > 0) ? round($lastMonth + 100 / $this->count, 2).'%' : '0%';

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

    }

    public function patients()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/profile-add.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('patient.titles');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $curr_month = Carbon::now();
        $cacheKey = 'dashboard_patients_'.auth()->user()->client_id.'_'.$curr_month->format('Y-m');

        $data = CacheHelper::remember(['dashboard', 'patients'], $cacheKey, 300, function () use ($curr_month) {
            $count = Patient::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->count();
            $lastMonth = Patient::whereRaw("created_at>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

            return ['count' => $count, 'lastMonth' => $lastMonth];
        });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        $this->change = ($this->count > 0) ? round($lastMonth + 100 / $this->count, 2).'%' : '0%';

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

    }

    public function encounters()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/scissor.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('encounter.titles');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $curr_month = Carbon::now();
        $cacheKey = 'dashboard_encounters_'.auth()->user()->client_id.'_'.$curr_month->format('Y-m');

        $data = CacheHelper::remember(['dashboard', 'encounters'], $cacheKey, 300, function () use ($curr_month) {
            $count = Encounter::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
            $lastMonth = Encounter::whereRaw("start>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

            return ['count' => $count, 'lastMonth' => $lastMonth];
        });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        $this->change = ($this->count > 0) ? round($lastMonth + 100 / $this->count, 2).'%' : '0%';
        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

    }

    public function invoices()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/scissor.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('invoices.earnings');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $this->symbol = '$';

        $curr_month = Carbon::now();
        $cacheKey = 'dashboard_invoices_'.auth()->user()->client_id.'_'.$curr_month->format('Y-m');

        $data = CacheHelper::remember(['dashboard', 'invoices'], $cacheKey, 300, function () use ($curr_month) {
            $count = Invoice::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->sum('total_net');
            $lastMonth = Invoice::whereRaw("created_at>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('total_net');

            return ['count' => $count, 'lastMonth' => $lastMonth];
        });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        $this->change = ($this->count > 0) ? round($lastMonth + 100 / $this->count, 2).'%' : '0%';

    }
}
