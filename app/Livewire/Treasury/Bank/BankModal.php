<?php

namespace App\Livewire\Treasury\Bank;

use App\Models\Accounting\AccountingAccount;
use App\Models\Treasury\Bank;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BankModal extends Component
{
    public ?Bank $bank = null;

    #[Validate]
    public string $bank_name = '';

    #[Validate]
    public string $account_number = '';

    #[Validate]
    public string $account_type = 'checking';

    #[Validate]
    public string $currency = 'PAB';

    #[Validate]
    public int $accounting_account_id = 0;

    #[Validate]
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
        $this->authorize('treasury.banks.manage');
        $this->validate();

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
        } else {
            Bank::create([
                'client_id' => $clientId,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_type' => $this->account_type,
                'currency' => $this->currency,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
        }

        $this->dispatch('bank-saved');
        $this->dispatch('closeModal');
    }

    public function delete(): void
    {
        if ($this->bank) {
            $this->authorize('treasury.banks.manage');
            $this->bank->delete();
            $this->dispatch('bank-saved');
            $this->dispatch('closeModal');
        }
    }

    public function getAccountingAccountsProperty()
    {
        return AccountingAccount::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->where('account_type', 'asset')
            ->orderBy('code')
            ->get();
    }

    public function render()
    {
        return view('livewire.treasury.bank.bank-modal', [
            'accounts' => $this->accountingAccounts,
        ]);
    }
}
