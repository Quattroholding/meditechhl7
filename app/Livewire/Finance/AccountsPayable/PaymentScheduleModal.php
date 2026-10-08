<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\PaymentSchedule;
use App\Models\Finance\SupplierInvoice;
use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Services\Finance\AccountsPayableService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentScheduleModal extends Component
{
    public bool $isModal = true;

    public SupplierInvoice $invoice;

    public array $schedules = [];

    public float $remainingAmount = 0;

    public bool $showModal = false;

    public ?int $defaultBankId = null;

    public ?int $defaultCashRegisterId = null;

    public function mount(SupplierInvoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->loadSchedules();
    }

    #[On('openPaymentModal')]
    public function openModal(): void
    {
        $this->showModal = true;
    }

    #[On('openPaymentScheduleFromDataTable')]
    public function openModalFromDataTable(): void
    {
        $this->showModal = true;
        $this->loadSchedules();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
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

        // Validar que al menos se seleccione banco o caja
        if (! $this->defaultBankId && ! $this->defaultCashRegisterId) {
            $this->dispatch('error', message: 'Debes seleccionar un banco o una caja para los pagos programados');

            return;
        }

        try {
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
                $service->createPaymentSchedule(
                    $this->invoice,
                    $scheduleData,
                    $this->defaultBankId,
                    $this->defaultCashRegisterId
                );
            }

            $this->dispatch('schedules-saved');
            $this->showModal = false;
        } catch (\Exception $e) {
            Log::error('Error saving payment schedules', ['error' => $e->getMessage()]);
            $this->dispatch('error', message: 'Error al guardar pagos programados: '.$e->getMessage());
        }
    }

    public function getBanksProperty(): Collection
    {
        return Bank::where('client_id', Auth::user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get();
    }

    public function getCashRegistersProperty(): Collection
    {
        return CashRegister::where('client_id', Auth::user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.finance.accounts-payable.payment-schedule-modal', [
            'invoice' => $this->invoice,
            'banks' => $this->banks,
            'cashRegisters' => $this->cashRegisters,
        ]);
    }
}
