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

    public $currentClient;

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

    public function mount()
    {
       $this->currentClient =  auth()->user()->getCurrentClient();
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
        $currentClient =  $this->currentClient;
        $cacheKey = 'dashboard_appointments_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'appointments'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Appointment::whereBetween('start', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->count();
                $lastMonth = Appointment::whereBetween('start', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient =  $this->currentClient;
        $cacheKey = 'dashboard_patients_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'patients'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Patient::whereBetween('created_at', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->count();
                $lastMonth = Patient::whereBetween('created_at', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient = $this->currentClient;
        $cacheKey = 'dashboard_encounters_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'encounters'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Encounter::whereBetween('start', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->count();
                $lastMonth = Encounter::whereBetween('start', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->count();

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient = $this->currentClient;
        $cacheKey = 'dashboard_invoices_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'invoices'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = Payment::whereBetween('payment_date', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('amount') ?? 0;
                $lastMonth = Payment::whereBetween('payment_date', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('amount') ?? 0;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient =  $this->currentClient;
        $cacheKey = 'dashboard_subscriptions_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'subscriptions'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $count = ClientInvoicePayment::whereBetween('payment_date', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('amount') ?? 0;
                $lastMonth = ClientInvoicePayment::whereBetween('payment_date', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('amount') ?? 0;

                return ['count' => $count, 'lastMonth' => $lastMonth];
            });

        $this->count = $data['count'];
        $lastMonth = $data['lastMonth'];

        if ($this->count > $lastMonth) {
            $this->class = 'passive-view';
        } else {
            $this->class = 'negative-view';
        }

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient = $this->currentClient;
        $cacheKey = 'dashboard_receivable_services_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'receivable_services'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $invoiced = Invoice::whereBetween('issue_date', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('total_net') ?? 0;
                $paid = Payment::whereBetween('payment_date', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('amount') ?? 0;
                $count = $invoiced - $paid;

                $lastMonthInvoiced = Invoice::whereBetween('issue_date', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('total_net') ?? 0;
                $lastMonthPaid = Payment::whereBetween('payment_date', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('amount') ?? 0;
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

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
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
        $currentClient =  $this->currentClient;
        $cacheKey = 'dashboard_receivable_subscriptions_'.$currentClient->id.'_'.$curr_month->format('Y-m');

        $data = Cache::tags(['dashboard', 'receivable_subscriptions'])
            ->remember($cacheKey, 300, function () use ($curr_month, $currentClient) {
                $invoiced = ClientInvoice::whereBetween('created_at', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('total') ?? 0;
                $paid = ClientInvoicePayment::whereBetween('payment_date', [$curr_month->copy()->startOfMonth(), $curr_month->copy()->endOfMonth()])
                    ->sum('amount') ?? 0;
                $count = $invoiced - $paid;

                $lastMonthInvoiced = ClientInvoice::whereBetween('created_at', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('total') ?? 0;
                $lastMonthPaid = ClientInvoicePayment::whereBetween('payment_date', [$curr_month->copy()->subMonth(1)->startOfMonth(), $curr_month->copy()->subMonth(1)->endOfMonth()])
                    ->sum('amount') ?? 0;
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

        if ($lastMonth > 0) {
            $percentageChange = (($this->count - $lastMonth) / $lastMonth) * 100;
            $this->change = round($percentageChange, 2).'%';
        } elseif ($this->count > 0) {
            $this->change = '100%';
        } else {
            $this->change = '0%';
        }
    }
}
