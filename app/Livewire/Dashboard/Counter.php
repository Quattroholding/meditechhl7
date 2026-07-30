<?php

namespace App\Livewire\Dashboard;

use App\Models\Appointment;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;


class Counter extends Component
{
    public $function;

    public $count = 0;

    public $icon;

    public $class;

    public $title;

    public $change = '0%';

    public $arrowClass;

    public $symbol = '';

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
        if ($this->function == 'subscriptions') {
            $this->subscriptions();
        }
        if ($this->function == 'receivable_services') {
            $this->receivableServices();
        }
        if ($this->function == 'receivable_subscriptions') {
            $this->receivableSubscriptions();
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
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_appointments_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'appointments'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Appointment::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
                $lastMonth = Appointment::whereRaw("start>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // 100% de incremento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

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
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_patients_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'patients'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Patient::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->count();
                $lastMonth = Patient::whereRaw("created_at>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // 100% de incremento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

    }

    public function encounters()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/dep-icon-01.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('encounter.titles');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $curr_month = Carbon::now();
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_encounters_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'encounters'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Encounter::whereRaw("start>='".$curr_month->format('Y-m-01')."' and end <='".$curr_month->format('Y-m-t')."'")->count();
                $lastMonth = Encounter::whereRaw("start>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and end <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // 100% de incremento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

    }

    public function invoices()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/tag-icon-02.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('invoices.earnings');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $this->symbol = '$';

        $curr_month = Carbon::now();
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_invoices_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'invoices'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Payment::whereRaw("payment_date>='".$curr_month->format('Y-m-01')."' and payment_date <='".$curr_month->format('Y-m-t')."'")->sum('amount') ?? 0;
                $lastMonth = Payment::whereRaw("payment_date>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and payment_date <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('amount') ?? 0;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // 100% de incremento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

    }

    public function subscriptions()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/tag-icon-02.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('subscriptions.earnings');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-up-right me-1';
        }

        $this->symbol = '$';

        $curr_month = Carbon::now();
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_subscriptions_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'subscriptions'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = ClientInvoicePayment::whereRaw("payment_date>='".$curr_month->format('Y-m-01')."' and payment_date <='".$curr_month->format('Y-m-t')."'")->sum('amount') ?? 0;
                $lastMonth = ClientInvoicePayment::whereRaw("payment_date>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and payment_date <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('amount') ?? 0;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // 100% de incremento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

    }

    public function receivableServices()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/tag-icon-03.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('invoices.receivable');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-down-right me-1';
        }

        $this->symbol = '$';

        $curr_month = Carbon::now();
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_receivable_services_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'receivable_services'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $invoiced = Invoice::whereRaw("issue_date>='".$curr_month->format('Y-m-01')."' and issue_date <='".$curr_month->format('Y-m-t')."'")->sum('total_net') ?? 0;
                $paid = Payment::whereRaw("payment_date>='".$curr_month->format('Y-m-01')."' and payment_date <='".$curr_month->format('Y-m-t')."'")->sum('amount') ?? 0;
                $count = $invoiced - $paid;

                $lastMonthInvoiced = Invoice::whereRaw("issue_date>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and issue_date <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('total_net') ?? 0;
                $lastMonthPaid = Payment::whereRaw("payment_date>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and payment_date <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('amount') ?? 0;
                $lastMonth = $lastMonthInvoiced - $lastMonthPaid;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count < $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // Aumento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

    }

    public function receivableSubscriptions()
    {

        if (empty($this->icon)) {
            $this->icon = url('/assets/img/icons/tag-icon-03.svg');
        }
        if (empty($this->title)) {
            $this->title = trans('subscriptions.receivable');
        }
        if (empty($this->arrowClass)) {
            $this->arrowClass = 'feather-arrow-down-right me-1';
        }

        $this->symbol = '$';

        $curr_month = Carbon::now();
        $currentClient = auth()->user()->getCurrentClient();
        $cacheKey = 'dashboard_receivable_subscriptions_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'receivable_subscriptions'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $invoiced = ClientInvoice::whereRaw("created_at>='".$curr_month->format('Y-m-01')."' and created_at <='".$curr_month->format('Y-m-t')."'")->sum('total') ?? 0;
                $paid = ClientInvoicePayment::whereRaw("payment_date>='".$curr_month->format('Y-m-01')."' and payment_date <='".$curr_month->format('Y-m-t')."'")->sum('amount') ?? 0;
                $count = $invoiced - $paid;

                $lastMonthInvoiced = ClientInvoice::whereRaw("created_at>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and created_at <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('total') ?? 0;
                $lastMonthPaid = ClientInvoicePayment::whereRaw("payment_date>='".$curr_month->copy()->subMonth(1)->format('Y-m-01')."' and payment_date <='".$curr_month->copy()->subMonth(1)->format('Y-m-t')."'")->sum('amount') ?? 0;
                $lastMonth = $lastMonthInvoiced - $lastMonthPaid;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count < $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        // Calcular el cambio porcentual correctamente
        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%'; // Aumento si el mes pasado era 0
        } else {
            $this->change = '0%'; // Ambos son 0
        }

    }
}
