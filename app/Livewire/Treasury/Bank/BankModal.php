<?php

namespace App\Livewire\Treasury\Bank;

use App\Models\Accounting\AccountingAccount;
use App\Models\Treasury\Bank;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class BankModal extends Component
{
    public bool $isModal = true;

    public ?Bank $bank = null;

    public string $bank_name = '';

    public string $account_number = '';

    public string $account_type = 'checking';

    public string $currency = 'PAB';

    public int $accounting_account_id = 0;

    public string $status = 'active';

    public function mount(?Bank $bank = null): void
    {
        if ($bank) {
            $this->bank = $bank;
            $this->bank_name = $bank->bank_name;
            $this->account_number = $bank->account_number;
            $this->account_type = $bank->account_type;
            $this->currency = $bank->currency;
            $this->accounting_account_id = $bank->accounting_account_id;
            $this->status = $bank->status;
        }
    }

    public function updatedBankName(): void
    {
        $this->validateOnly('bank_name');
    }

    public function updatedAccountNumber(): void
    {
        $this->validateOnly('account_number');
    }

    public function updatedAccountingAccountId(): void
    {
        $this->validateOnly('accounting_account_id');
    }

    protected $messages = [
        'bank_name.required' => 'El nombre del banco es obligatorio.',
        'bank_name.string' => 'El nombre del banco debe ser texto.',
        'bank_name.max' => 'El nombre del banco no puede exceder 255 caracteres.',
        'account_number.required' => 'El número de cuenta es obligatorio.',
        'account_number.string' => 'El número de cuenta debe ser texto.',
        'account_number.max' => 'El número de cuenta no puede exceder 50 caracteres.',
        'account_type.required' => 'El tipo de cuenta es obligatorio.',
        'account_type.in' => 'El tipo de cuenta debe ser válido.',
        'currency.required' => 'La moneda es obligatoria.',
        'currency.in' => 'La moneda debe ser PAB, USD o EUR.',
        'accounting_account_id.required' => 'La cuenta contable es obligatoria.',
        'accounting_account_id.exists' => 'La cuenta contable seleccionada no existe.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado debe ser válido.',
    ];

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_type' => 'required|in:checking,savings,money_market,credit_line',
            'currency' => 'required|in:PAB,USD,EUR',
            'accounting_account_id' => 'required|exists:accounting_accounts,id',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    public function save(): void
    {
        $this->validate();
        $this->authorize('treasury.banks.manage');

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->bank) {
            $this->bank->update([
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_type' => $this->account_type,
                'currency' => $this->currency,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Banco actualizado exitosamente.');
        } else {
            Bank::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_type' => $this->account_type,
                'currency' => $this->currency,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Banco creado exitosamente.');
        }

        if ($this->isModal) {
            $this->dispatch('bank-saved');
            $this->dispatch('closeModal');
        } else {
            $this->redirect(route('treasury.banks.index'));
        }
    }

    public function delete(): void
    {
        if ($this->bank) {
            $this->authorize('treasury.banks.manage');
            $this->bank->delete();
            session()->flash('message.success', 'Banco eliminado exitosamente.');

            if ($this->isModal) {
                $this->dispatch('bank-saved');
                $this->dispatch('closeModal');
            } else {
                $this->redirect(route('treasury.banks.index'));
            }
        }
    }

    public function getAccountingAccountsProperty(): Collection
    {
        return AccountingAccount::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->where('account_type', 'asset')
            ->orderBy('code')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.treasury.bank.bank-modal', [
            'accounts' => $this->accountingAccounts,
        ]);
    }
}
