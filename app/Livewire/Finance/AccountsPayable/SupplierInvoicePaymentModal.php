<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\SupplierInvoice;
use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Models\Treasury\TreasuryMovement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SupplierInvoicePaymentModal extends Component
{
    public bool $showModal = false;

    public ?SupplierInvoice $invoice = null;

    #[Validate]
    public float $amount = 0;

    #[Validate]
    public string $payment_date = '';

    #[Validate]
    public ?int $bank_id = null;

    #[Validate]
    public ?int $cash_register_id = null;

    #[Validate]
    public string $description = '';

    #[Validate]
    public ?string $reference_number = null;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date',
        'bank_id' => 'nullable|exists:banks,id',
        'cash_register_id' => 'nullable|exists:cash_registers,id',
        'description' => 'required|string|max:500',
        'reference_number' => 'nullable|string|max:100',
    ];

    public function mount(?SupplierInvoice $invoice = null): void
    {
        if ($invoice) {
            $this->invoice = $invoice;
            $this->amount = $invoice->balance > 0 ? $invoice->balance : 0;
            $this->payment_date = now()->toDateString();
            $this->description = "Pago de factura {$invoice->invoice_number} - {$invoice->supplier->legal_name}";
        }
    }

    #[On('openPaymentImmediateModal')]
    public function openModal(): void
    {
        $this->showModal = true;
    }

    #[On('openPaymentImmediateFromDataTable')]
    public function openModalFromDataTable(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['amount', 'bank_id', 'cash_register_id', 'description', 'reference_number']);
        $this->payment_date = now()->toDateString();
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->invoice) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Factura no encontrada.',
            );

            return;
        }

        // Validate that at least one destination is selected
        if (! $this->bank_id && ! $this->cash_register_id) {
            $this->addError('bank_id', 'Debe seleccionar un banco o una caja.');

            return;
        }

        // Validate amount doesn't exceed balance
        if ($this->amount > $this->invoice->balance) {
            $this->addError('amount', 'El monto no puede ser mayor al saldo pendiente de B/. '.number_format($this->invoice->balance, 2));

            return;
        }

        try {
            DB::transaction(function () {
                $clientId = Auth::user()->getCurrentClient()->id;

                // Generar número de movimiento
                $movementNumber = $this->generateMovementNumber($clientId);

                // Crear movimiento de tesorería (egreso)
                $movement = TreasuryMovement::create([
                    'client_id' => $clientId,
                    'movement_number' => $movementNumber,
                    'movement_date' => $this->payment_date,
                    'movement_type' => 'withdrawal',
                    'bank_id' => $this->bank_id,
                    'cash_register_id' => $this->cash_register_id,
                    'amount' => $this->amount,
                    'description' => $this->description,
                    'reference_number' => $this->reference_number,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Crear payment schedule para registrar este pago
                $this->invoice->paymentSchedules()->create([
                    'uuid' => Str::uuid(),
                    'payment_date' => $this->payment_date,
                    'amount' => $this->amount,
                    'paid' => true,
                    'paid_at' => now(),
                    'treasury_movement_id' => $movement->id,
                    'notes' => 'Pago inmediato registrado',
                    'updated_by' => Auth::id(),
                    'bank_id' => $this->bank_id,
                    'cash_register_id' => $this->cash_register_id,
                ]);

                // Actualizar balance de factura
                $paidAmount = $this->invoice->paymentSchedules()
                    ->where('paid', true)
                    ->sum('amount');

                $newBalance = $this->invoice->total_amount - $paidAmount;
                $newStatus = $newBalance <= 0.01 ? 'paid' : 'partial';

                $this->invoice->update([
                    'paid_amount' => $paidAmount,
                    'balance' => $newBalance,
                    'status' => $newStatus,
                ]);

                Log::info('Supplier invoice payment registered', [
                    'invoice_id' => $this->invoice->id,
                    'invoice_number' => $this->invoice->invoice_number,
                    'amount' => $this->amount,
                    'movement_id' => $movement->id,
                    'movement_number' => $movement->movement_number,
                ]);
            });

            $this->dispatch('showToastr',
                type: 'success',
                message: '¡Pago registrado exitosamente!',
            );

            $this->dispatch('paymentRegistered');
            $this->closeModal();

        } catch (\Exception $e) {
            Log::error('Error registering supplier invoice payment', ['error' => $e->getMessage()]);
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al registrar el pago: '.$e->getMessage(),
            );
        }
    }

    private function generateMovementNumber(int $clientId): string
    {
        $year = now()->year;
        $lastMovement = TreasuryMovement::where('client_id', $clientId)
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $sequence = $lastMovement ? (int) substr($lastMovement->movement_number, -6) + 1 : 1;

        return sprintf('TM-%d-%06d', $year, $sequence);
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
        return view('livewire.finance.accounts-payable.supplier-invoice-payment-modal', [
            'banks' => $this->banks,
            'cashRegisters' => $this->cashRegisters,
        ]);
    }
}
