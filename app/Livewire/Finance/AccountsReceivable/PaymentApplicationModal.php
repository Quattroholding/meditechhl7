<?php

namespace App\Livewire\Finance\AccountsReceivable;

use App\Models\AccountsReceivable;
use App\Services\Finance\AccountsReceivableService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PaymentApplicationModal extends Component
{
    public AccountsReceivable $receivable;

    #[Validate]
    public float $amount_to_apply = 0;

    public string $notes = '';

    public function mount(AccountsReceivable $receivable): void
    {
        $this->receivable = $receivable;
        $this->amount_to_apply = $receivable->balance;
    }

    public function rules(): array
    {
        return [
            'amount_to_apply' => [
                'required',
                'numeric',
                'min:0.01',
                'max:'.$this->receivable->balance,
            ],
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function apply(): void
    {
        $this->authorize('receivables.apply-payment');
        $this->validate();

        $service = app(AccountsReceivableService::class);
        $service->applyPayment($this->receivable, $this->amount_to_apply);

        $this->dispatch('payment-applied');
        $this->dispatch('closePaymentModal');
    }

    public function render()
    {
        return view('livewire.finance.accounts-receivable.payment-application-modal', [
            'receivable' => $this->receivable,
        ]);
    }
}
