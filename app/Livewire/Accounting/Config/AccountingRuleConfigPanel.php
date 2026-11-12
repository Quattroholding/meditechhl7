<?php

namespace App\Livewire\Accounting\Config;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingRule;
use App\Models\Accounting\ClientAccountingRuleMapping;
use Illuminate\View\View;
use Livewire\Component;

class AccountingRuleConfigPanel extends Component
{
    public int $clientId;

    public array $rules = [
        'income' => [],
        'receivable' => [],
        'expense' => [],
        'bank' => [],
    ];

    public array $accounts = [];

    public string $selectedRule = 'income';

    public string $selectedAccountId = '';

    public bool $showSuccess = false;

    public string $successMessage = '';

    public function mount(): void
    {
        $this->authorize('accounting.config.manage');
        $this->clientId = auth()->user()->getCurrentClient()->id;
        $this->loadRules();
        $this->loadAccounts();
    }

    public function loadRules(): void
    {
        foreach (['income', 'receivable', 'expense', 'bank'] as $ruleType) {
            $mapping = ClientAccountingRuleMapping::where([
                ['client_id', $this->clientId],
                ['rule_type', $ruleType],
                ['status', 'active'],
            ])->with('accountingAccount', 'accountingRule')->first();

            $this->rules[$ruleType] = $mapping ? [
                'id' => $mapping->id,
                'account_id' => $mapping->accounting_account_id,
                'account_code' => $mapping->accountingAccount->code,
                'account_name' => $mapping->accountingAccount->name,
                'rule_code' => $mapping->accountingRule->code,
                'rule_name' => $mapping->accountingRule->name,
            ] : null;
        }
    }

    public function loadAccounts(): void
    {
        $this->accounts = AccountingAccount::where('client_id', $this->clientId)
            ->where('status', 'active')
            ->orderBy('code')
            ->get()
            ->map(fn ($acc) => [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $acc->account_type->value,
            ])
            ->toArray();
    }

    public function updateRule(): void
    {
        $this->validate([
            'selectedAccountId' => 'required|numeric',
            'selectedRule' => 'required|in:income,receivable,expense,bank',
        ]);

        // Verify the selected account belongs to this client
        $account = AccountingAccount::where('id', $this->selectedAccountId)
            ->where('client_id', $this->clientId)
            ->firstOrFail();

        // Determine which rule to link based on account type
        $ruleCode = match ($this->selectedRule) {
            'income' => 'RULE-INCOME-001',
            'receivable' => 'RULE-ASSET-001',
            'bank' => 'RULE-ASSET-001',
            'expense' => 'RULE-EXPENSE-001',
            default => 'RULE-ASSET-001',
        };

        $rule = AccountingRule::where('code', $ruleCode)->firstOrFail();

        ClientAccountingRuleMapping::updateOrCreate(
            [
                'client_id' => $this->clientId,
                'rule_type' => $this->selectedRule,
            ],
            [
                'accounting_rule_id' => $rule->id,
                'accounting_account_id' => $this->selectedAccountId,
                'status' => 'active',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        $this->loadRules();
        $this->showSuccessMessage("Regla de {$this->selectedRule} actualizada exitosamente");
        $this->selectedAccountId = '';
    }

    public function render(): View
    {
        return view('livewire.accounting.config.accounting-rule-config-panel');
    }
}
