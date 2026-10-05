<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\PaymentSchedule;
use App\Models\SupplierInvoice;
use App\Services\Finance\AccountsPayableService;
use Livewire\Component;

class PaymentScheduleModal extends Component
{
    public SupplierInvoice $invoice;

    public array $schedules = [];

    public float $remainingAmount = 0;

    public bool $showModal = false;

    public function mount(SupplierInvoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->loadSchedules();
    }

    public function loadSchedules(): void
    {
        $this->schedules = $this->invoice->paymentSchedules()
            ->orderBy('payment_date')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'payment_date' => $s->payment_date->toDateString(),
                'amount' => $s->amount,
                'paid' => $s->paid,
                'paid_at' => $s->paid_at?->toDateString(),
            ])
            ->toArray();

        $this->calculateRemaining();
    }

    public function calculateRemaining(): void
    {
        $scheduledTotal = collect($this->schedules)->sum('amount');
        $this->remainingAmount = $this->invoice->total_amount - $scheduledTotal;
    }

    public function addSchedule(): void
    {
        $this->schedules[] = [
            'id' => null,
            'payment_date' => now()->toDateString(),
            'amount' => 0,
            'paid' => false,
            'paid_at' => null,
        ];
    }

    public function removeSchedule(int $index): void
    {
        if (isset($this->schedules[$index]['id'])) {
            PaymentSchedule::find($this->schedules[$index]['id'])->delete();
        }
        unset($this->schedules[$index]);
        $this->schedules = array_values($this->schedules);
        $this->calculateRemaining();
    }

    public function save(): void
    {
        $this->authorize('payables.invoices.create');

        // Validar que el total sea igual al monto de la factura
        $total = collect($this->schedules)->sum('amount');
        if (abs($total - $this->invoice->total_amount) > 0.01) {
            $this->dispatch('error', message: 'El total del programa debe ser igual al monto de la factura');

            return;
        }

        $service = app(AccountsPayableService::class);

        // Eliminar schedules anteriores
        $this->invoice->paymentSchedules()->delete();

        // Crear nuevos schedules
        $scheduleData = [];
        foreach ($this->schedules as $schedule) {
            if ($schedule['amount'] > 0) {
                $scheduleData[] = [
                    'payment_date' => $schedule['payment_date'],
                    'amount' => $schedule['amount'],
                    'notes' => null,
                ];
            }
        }

        if (! empty($scheduleData)) {
            $service->createPaymentSchedule($this->invoice, $scheduleData);
        }

        $this->dispatch('schedules-saved');
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.finance.accounts-payable.payment-schedule-modal', [
            'invoice' => $this->invoice,
        ]);
    }
}
