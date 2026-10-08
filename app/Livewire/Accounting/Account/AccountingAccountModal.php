<?php

namespace App\Livewire\Accounting\Account;

use App\Models\Accounting\AccountingAccount;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AccountingAccountModal extends Component
{
    public bool $isModal = true;

    public ?AccountingAccount $account = null;

    #[Validate]
    public string $code = '';

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $account_type = '';

    #[Validate]
    public ?int $parent_id = null;

    #[Validate]
    public bool $allows_transaction = false;

    #[Validate]
    public string $status = 'active';

    public int $level = 0;

    public string $description = '';

    protected $rules = [
        'description' => 'nullable|string|max:1000',
    ];

    public function mount(?AccountingAccount $account = null): void
    {
        if ($account) {
            $this->account = $account;
            $this->code = $account->code;
            $this->name = $account->name;
            $this->account_type = $account->account_type;
            $this->parent_id = $account->parent_id;
            $this->allows_transaction = $account->allows_transaction;
            $this->status = $account->status;
            $this->level = $account->level;
            $this->description = $account->description ?? '';
        }
    }

    public function rules(): array
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('accounting_accounts', 'code')
                    ->where('client_id', $clientId)
                    ->ignore($this->account?->id),
            ],
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,income,expense,cost',
            'parent_id' => 'nullable|exists:accounting_accounts,id',
            'allows_transaction' => 'boolean',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function calculateLevel(): int
    {
        if (! $this->parent_id) {
            return 0;
        }

        $parent = AccountingAccount::find($this->parent_id);

        return $parent ? $parent->level + 1 : 0;
    }

    public function updatedParentId(): void
    {
        $this->level = $this->calculateLevel();
    }

    public function save(): void
    {
        $this->authorize('accounting.accounts.manage');
        $this->validate();

        $clientId = auth()->user()->getCurrentClient()->id;
        $this->level = $this->calculateLevel();

        if ($this->account) {
            $this->account->update([
                'code' => $this->code,
                'name' => $this->name,
                'account_type' => $this->account_type,
                'parent_id' => $this->parent_id,
                'level' => $this->level,
                'allows_transaction' => $this->allows_transaction,
                'status' => $this->status,
                'description' => $this->description,
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Cuenta contable actualizada exitosamente.');
        } else {
            AccountingAccount::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'code' => $this->code,
                'name' => $this->name,
                'account_type' => $this->account_type,
                'parent_id' => $this->parent_id,
                'level' => $this->level,
                'allows_transaction' => $this->allows_transaction,
                'status' => $this->status,
                'description' => $this->description,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Cuenta contable creada exitosamente.');
        }

        if ($this->isModal) {
            $this->dispatch('account-saved');
            $this->dispatch('closeModal');
            $this->redirect(route('accounting.accounts'));
        } else {
            $this->redirect(route('accounting.accounts'));
        }
    }

    public function delete(): void
    {
        if ($this->account) {
            $this->authorize('accounting.accounts.manage');
            $this->account->delete();
            session()->flash('message.success', 'Cuenta contable eliminada exitosamente.');

            if ($this->isModal) {
                $this->dispatch('account-saved');
                $this->dispatch('closeModal');
            } else {
                $this->redirect(route('accounting.accounts.index'));
            }
        }
    }

    public function getParentAccountsProperty()
    {
        return AccountingAccount::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->where('id', '!=', $this->account?->id)
            ->orderBy('code')
            ->get();
    }

    public function render()
    {
        return view('livewire.accounting.account.accounting-account-modal', [
            'parentAccounts' => $this->parentAccounts,
        ]);
    }
}
