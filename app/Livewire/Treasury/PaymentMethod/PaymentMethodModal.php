<?php

namespace App\Livewire\Treasury\PaymentMethod;

use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Models\Treasury\PaymentMethod;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PaymentMethodModal extends Component
{
    public bool $isModal = true;

    public ?PaymentMethod $paymentMethod = null;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $destination_type = 'bank';

    #[Validate]
    public ?int $default_bank_id = null;

    #[Validate]
    public ?int $default_cash_register_id = null;

    #[Validate]
    public string $status = 'active';

    public function mount(?PaymentMethod $paymentMethod = null): void
    {
        if ($paymentMethod) {
            $this->paymentMethod = $paymentMethod;
            $this->name = $paymentMethod->name;
            $this->destination_type = $paymentMethod->destination_type;
            $this->default_bank_id = $paymentMethod->default_bank_id;
            $this->default_cash_register_id = $paymentMethod->default_cash_register_id;
            $this->status = $paymentMethod->status;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'destination_type' => 'required|in:bank,cash',
            'default_bank_id' => 'nullable|exists:banks,id',
            'default_cash_register_id' => 'nullable|exists:cash_registers,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save(): void
    {
        $this->authorize('treasury.payment-methods.manage');
        $this->validate();

        // Validar que se seleccione el destino correcto según el tipo
        if ($this->destination_type === 'bank' && ! $this->default_bank_id) {
            $this->addError('default_bank_id', 'Debe seleccionar un banco.');

            return;
        }

        if ($this->destination_type === 'cash' && ! $this->default_cash_register_id) {
            $this->addError('default_cash_register_id', 'Debe seleccionar una caja.');

            return;
        }

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->paymentMethod) {
            $this->paymentMethod->update([
                'name' => $this->name,
                'destination_type' => $this->destination_type,
                'default_bank_id' => $this->destination_type === 'bank' ? $this->default_bank_id : null,
                'default_cash_register_id' => $this->destination_type === 'cash' ? $this->default_cash_register_id : null,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ]);
        } else {
            PaymentMethod::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'name' => $this->name,
                'destination_type' => $this->destination_type,
                'default_bank_id' => $this->destination_type === 'bank' ? $this->default_bank_id : null,
                'default_cash_register_id' => $this->destination_type === 'cash' ? $this->default_cash_register_id : null,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
        }

        $this->dispatch('payment-method-saved');
        $this->dispatch('closeModal');
    }

    public function delete(): void
    {
        if ($this->paymentMethod) {
            $this->authorize('treasury.payment-methods.manage');
            $this->paymentMethod->delete();
            $this->dispatch('payment-method-saved');
            $this->dispatch('closeModal');
        }
    }

    public function getBanksProperty()
    {
        return Bank::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get();
    }

    public function getCashRegistersProperty()
    {
        return CashRegister::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.treasury.payment-method.payment-method-modal', [
            'banks' => $this->banks,
            'cashRegisters' => $this->cashRegisters,
        ]);
    }
}
