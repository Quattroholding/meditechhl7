<?php

namespace App\Livewire\Dashboard;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use Carbon\Carbon;
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
        if($this->function =='appointments') $this->appointments();
        if($this->function =='patients') $this->patients();
        if($this->function =='encounters') $this->encounters();
        if($this->function =='invoices') $this->invoices();

        return view('livewire.dashboard.counter');
    }

    public function appointments(){

        if(empty($this->icon))  $this->icon=url('/assets/img/icons/calendar.svg');
        if(empty($this->title)) $this->title=trans('appointment.titles');
        if(empty($this->arrowClass)) $this->arrowClass='feather-arrow-up-right me-1';

        $curr_month = Carbon::now();
        $this->count = Appointment::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
        $lastMonth = Appointment::whereRaw("start>='".$curr_month->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->subMonth(1)->format('Y-m-t')."'")->count();

        //$this->change = round($lastMonth+100/$this->count,2).'%';
        $this->change = ($this->count > 0) ? round($lastMonth+100/$this->count,2).'%' : '0%';

        if($this->count>$lastMonth){
            $this->class='passive-view';
        }else{
            $this->class='negative-view';
        }


    }

    public function patients(){

        if(empty($this->icon))  $this->icon=url('/assets/img/icons/profile-add.svg');
        if(empty($this->title)) $this->title=trans('patient.titles');
        if(empty($this->arrowClass)) $this->arrowClass='feather-arrow-up-right me-1';

        $curr_month = Carbon::now();
        $this->count = Patient::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->count();
        $lastMonth = Patient::whereRaw("created_at>='".$curr_month->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->subMonth(1)->format('Y-m-t')."'")->count();
        //dd($this->count);
        //$this->change = round($lastMonth+100/$this->count).'%';
        $this->change = ($this->count > 0) ? round($lastMonth+100/$this->count,2).'%' : '0%';
        
        if($this->count>$lastMonth){
            $this->class='passive-view';
        }else{
            $this->class='negative-view';
        }

    }

    public function encounters(){

        if(empty($this->icon))  $this->icon=url('/assets/img/icons/scissor.svg');
        if(empty($this->title)) $this->title=trans('encounter.titles');
        if(empty($this->arrowClass)) $this->arrowClass='feather-arrow-up-right me-1';

        $curr_month = Carbon::now();
        $this->count = Encounter::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
        $lastMonth = Encounter::whereRaw("start>='".$curr_month->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->subMonth(1)->format('Y-m-t')."'")->count();
        //d($this->count);
        //$this->change = round($lastMonth+100/$this->count,2).'%';
        $this->change = ($this->count > 0) ? round($lastMonth+100/$this->count,2).'%' : '0%';
        if($this->count>$lastMonth){
            $this->class='passive-view';
        }else{
            $this->class='negative-view';
        }

    }

    public function invoices(){

        if(empty($this->icon))  $this->icon=url('/assets/img/icons/scissor.svg');
        if(empty($this->title)) $this->title=trans('invoices.earnings');
        if(empty($this->arrowClass)) $this->arrowClass='feather-arrow-up-right me-1';

        $this->symbol='$';

        $curr_month = Carbon::now();
        $this->count = Invoice::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->sum('total_net');
        $lastMonth = Invoice::whereRaw("created_at>='".$curr_month->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->subMonth(1)->format('Y-m-t')."'")->sum('total_net');

        if($this->count>$lastMonth){
            $this->class='passive-view';
        }else{
            $this->class='negative-view';
        }
        //dd($this->class);
        /*dd($this->count);
        if($this->count>0){
            $this->change = round($lastMonth+100/$this->count,2).'%';
        }else{
            $this->change = '0%';
        }*/
        $this->change = ($this->count > 0) ? round($lastMonth+100/$this->count,2).'%' : '0%';
        //dd($this->change);




    }
}
