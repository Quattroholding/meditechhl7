<?php

namespace App\Livewire\Treasury\CashRegister;

use App\Models\Accounting\AccountingAccount;
use App\Models\Branch;
use App\Models\Treasury\CashRegister;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class CashRegisterModal extends Component
{
    public bool $isModal = true;

    public ?CashRegister $cashRegister = null;

    public string $name = '';

    public int $branch_id = 0;

    public int $responsible_user_id = 0;

    public int $accounting_account_id = 0;

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

    public function updatedName(): void
    {
        $this->validateOnly('name');
    }

    public function updatedBranchId(): void
    {
        $this->validateOnly('branch_id');
    }

    public function updatedResponsibleUserId(): void
    {
        $this->validateOnly('responsible_user_id');
    }

    public function updatedAccountingAccountId(): void
    {
        $this->validateOnly('accounting_account_id');
    }

    protected $messages = [
        'name.required' => 'El nombre de la caja es obligatorio.',
        'name.string' => 'El nombre debe ser texto.',
        'name.max' => 'El nombre no puede exceder 255 caracteres.',
        'branch_id.required' => 'La sucursal es obligatoria.',
        'branch_id.exists' => 'La sucursal seleccionada no existe.',
        'responsible_user_id.required' => 'El usuario responsable es obligatorio.',
        'responsible_user_id.exists' => 'El usuario seleccionado no existe.',
        'accounting_account_id.required' => 'La cuenta contable es obligatoria.',
        'accounting_account_id.exists' => 'La cuenta contable seleccionada no existe.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado debe ser activo, cerrado o inactivo.',
    ];

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
        $this->validate();
        $this->authorize('treasury.cash-registers.manage');

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
                'uuid' => Str::uuid(),
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

    public function getBranchesProperty(): Collection
    {
        return Branch::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('active', 1)
            ->orderBy('name')
            ->get();
    }

    public function getUsersProperty(): Collection
    {
        return User::where('default_client_id', auth()->user()->getCurrentClient()->id)
            ->where('active', 1)
            ->orderBy('first_name')
            ->get();
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
        return view('livewire.treasury.cash-register.cash-register-modal', [
            'branches' => $this->branches,
            'users' => $this->users,
            'accounts' => $this->accountingAccounts,
        ]);
    }
}
