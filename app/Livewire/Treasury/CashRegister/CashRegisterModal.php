<?php

namespace App\Livewire\Treasury\CashRegister;

use App\Models\Accounting\AccountingAccount;
use App\Models\Branch;
use App\Models\Treasury\CashRegister;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CashRegisterModal extends Component
{
    public bool $isModal = true;

    public ?CashRegister $cashRegister = null;

    #[Validate]
    public string $name = '';

    #[Validate]
    public int $branch_id = 0;

    #[Validate]
    public int $responsible_user_id = 0;

    #[Validate]
    public int $accounting_account_id = 0;

    #[Validate]
    public string $status = 'active';

    public function mount(?CashRegister $cashRegister = null): void
    {
        if ($cashRegister) {
            $this->cashRegister = $cashRegister;
            $this->name = $cashRegister->name;
            $this->branch_id = $cashRegister->branch_id;
            $this->responsible_user_id = $cashRegister->responsible_user_id;
            $this->accounting_account_id = $cashRegister->accounting_account_id;
            $this->status = $cashRegister->status;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'responsible_user_id' => 'required|exists:users,id',
            'accounting_account_id' => 'required|exists:accounting_accounts,id',
            'status' => 'required|in:active,closed,inactive',
        ];
    }

    public function save(): void
    {
        $this->authorize('treasury.cash-registers.manage');
        $this->validate();

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->cashRegister) {
            $this->cashRegister->update([
                'name' => $this->name,
                'branch_id' => $this->branch_id,
                'responsible_user_id' => $this->responsible_user_id,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ]);
        } else {
            CashRegister::create([
                'client_id' => $clientId,
                'name' => $this->name,
                'branch_id' => $this->branch_id,
                'responsible_user_id' => $this->responsible_user_id,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
        }

        $this->dispatch('cash-register-saved');
        $this->dispatch('closeModal');
    }

    public function delete(): void
    {
        if ($this->cashRegister) {
            $this->authorize('treasury.cash-registers.manage');
            $this->cashRegister->delete();
            $this->dispatch('cash-register-saved');
            $this->dispatch('closeModal');
        }
    }

    public function getBranchesProperty()
    {
        return Branch::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('active', 1)
            ->orderBy('name')
            ->get();
    }

    public function getUsersProperty()
    {
        return User::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('active', 1)
            ->orderBy('name')
            ->get();
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
        return view('livewire.treasury.cash-register.cash-register-modal', [
            'branches' => $this->branches,
            'users' => $this->users,
            'accounts' => $this->accountingAccounts,
        ]);
    }
}
