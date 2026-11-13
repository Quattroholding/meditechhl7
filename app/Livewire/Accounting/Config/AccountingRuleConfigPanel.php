<?php

namespace App\Livewire\Accounting\Config;

use App\Models\Accounting\AccountingAccount;
use App\Models\Accounting\AccountingRule;
use App\Models\Accounting\ClientAccountingRuleMapping;
use App\Models\MedicalSpeciality;
use Illuminate\View\View;
use Livewire\Component;

class AccountingRuleConfigPanel extends Component
{
    public int $clientId;

    // Tabs
    public string $currentTab = 'income';

    // Income rules (by specialty)
    public array $incomeRules = [];

    public int|string $selectedSpecialtyId = '';

    public int|string $selectedIncomeAccountId = '';

    // Receivable rules (by payment source)
    public array $receivableRules = [];

    public string $selectedPaymentSource = '';

    public int|string $selectedReceivableAccountId = '';

    // Bank rule (simple, one per client)
    public array $bankRule = [];

    // Catalogs
    public array $specialties = [];

    public array $incomeAccounts = [];

    public array $receivableAccounts = [];

    public array $paymentSources = [
        'patient' => 'Paciente',
        'insurance' => 'Seguro',
    ];

    // UI State
    public bool $showSuccess = false;

    public string $successMessage = '';

    public function mount(): void
    {
        $this->authorize('accounting.config.manage');
        $this->clientId = auth()->user()->getCurrentClient()->id;
        $this->loadCatalogs();
        $this->loadRules();
    }

    public function loadCatalogs(): void
    {
        // Load medical specialties
        $this->specialties = MedicalSpeciality::where('deleted_at', null)
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
            ->toArray();

        // Load income accounts (type = 'income')
        $this->incomeAccounts = AccountingAccount::where([
            ['client_id', $this->clientId],
            ['account_type', 'income'],
            ['status', 'active'],
        ])
            ->orderBy('code')
            ->get()
            ->map(fn ($a) => ['id' => $a->id, 'code' => $a->code, 'name' => $a->name])
            ->toArray();

        // Load receivable accounts (type = 'asset')
        $this->receivableAccounts = AccountingAccount::where([
            ['client_id', $this->clientId],
            ['account_type', 'asset'],
            ['status', 'active'],
        ])
            ->orderBy('code')
            ->get()
            ->map(fn ($a) => ['id' => $a->id, 'code' => $a->code, 'name' => $a->name])
            ->toArray();
    }

    public function loadRules(): void
    {
        // Load income rules by specialty
        $this->incomeRules = ClientAccountingRuleMapping::where([
            ['client_id', $this->clientId],
            ['rule_type', 'income'],
            ['status', 'active'],
        ])
            ->with(['accountingAccount', 'medicalSpeciality'])
            ->orderBy('priority', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'specialty_id' => $r->medical_speciality_id,
                'specialty_name' => $r->medicalSpeciality?->name ?? 'Por Defecto',
                'account_id' => $r->accounting_account_id,
                'account_code' => $r->accountingAccount->code,
                'account_name' => $r->accountingAccount->name,
                'is_default' => $r->is_default,
            ])
            ->toArray();

        // Load receivable rules by payment source
        $this->receivableRules = ClientAccountingRuleMapping::where([
            ['client_id', $this->clientId],
            ['rule_type', 'receivable'],
            ['status', 'active'],
        ])
            ->with('accountingAccount')
            ->orderBy('priority', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'payment_source' => $r->payment_source,
                'payment_source_label' => $r->payment_source ? $this->paymentSources[$r->payment_source] : 'Por Defecto',
                'account_id' => $r->accounting_account_id,
                'account_code' => $r->accountingAccount->code,
                'account_name' => $r->accountingAccount->name,
                'is_default' => $r->is_default,
            ])
            ->toArray();

        // Load bank rule
        $bankMapping = ClientAccountingRuleMapping::where([
            ['client_id', $this->clientId],
            ['rule_type', 'bank'],
            ['status', 'active'],
        ])
            ->with('accountingAccount')
            ->first();

        $this->bankRule = $bankMapping ? [
            'id' => $bankMapping->id,
            'account_code' => $bankMapping->accountingAccount->code,
            'account_name' => $bankMapping->accountingAccount->name,
        ] : [];
    }

    public function addIncomeRuleBySpecialty(): void
    {
        $this->validate([
            'selectedSpecialtyId' => 'required|exists:medical_specialties,id',
            'selectedIncomeAccountId' => 'required|exists:accounting_accounts,id',
        ], [
            'selectedSpecialtyId.required' => 'Debe seleccionar una especialidad',
            'selectedIncomeAccountId.required' => 'Debe seleccionar una cuenta contable',
        ]);

        // Verify the account belongs to this client
        AccountingAccount::where('id', $this->selectedIncomeAccountId)
            ->where('client_id', $this->clientId)
            ->firstOrFail();

        $incomeRule = AccountingRule::where('code', 'RULE-INCOME-001')->firstOrFail();

        ClientAccountingRuleMapping::updateOrCreate(
            [
                'client_id' => $this->clientId,
                'rule_type' => 'income',
                'medical_speciality_id' => $this->selectedSpecialtyId,
            ],
            [
                'accounting_rule_id' => $incomeRule->id,
                'accounting_account_id' => $this->selectedIncomeAccountId,
                'status' => 'active',
                'is_default' => false,
                'priority' => 10,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        $this->loadRules();
        $this->selectedSpecialtyId = '';
        $this->selectedIncomeAccountId = '';
        $this->showSuccessMessage('Regla de ingresos por especialidad agregada correctamente');
    }

    public function addReceivableRuleByPaymentSource(): void
    {
        $this->validate([
            'selectedPaymentSource' => 'required|in:patient,insurance',
            'selectedReceivableAccountId' => 'required|exists:accounting_accounts,id',
        ], [
            'selectedPaymentSource.required' => 'Debe seleccionar un tipo de pagador',
            'selectedReceivableAccountId.required' => 'Debe seleccionar una cuenta contable',
        ]);

        // Verify the account belongs to this client
        AccountingAccount::where('id', $this->selectedReceivableAccountId)
            ->where('client_id', $this->clientId)
            ->firstOrFail();

        $assetRule = AccountingRule::where('code', 'RULE-ASSET-001')->firstOrFail();

        ClientAccountingRuleMapping::updateOrCreate(
            [
                'client_id' => $this->clientId,
                'rule_type' => 'receivable',
                'payment_source' => $this->selectedPaymentSource,
            ],
            [
                'accounting_rule_id' => $assetRule->id,
                'accounting_account_id' => $this->selectedReceivableAccountId,
                'status' => 'active',
                'is_default' => false,
                'priority' => 10,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        );

        $this->loadRules();
        $this->selectedPaymentSource = '';
        $this->selectedReceivableAccountId = '';
        $this->showSuccessMessage('Regla de CxC por tipo de pagador agregada correctamente');
    }

    public function deleteRule(int $ruleId): void
    {
        $mapping = ClientAccountingRuleMapping::findOrFail($ruleId);

        // Verify it belongs to this client
        if ($mapping->client_id !== $this->clientId) {
            $this->addError('delete', 'No tiene permisos para eliminar esta regla');

            return;
        }

        // Prevent deletion of default rules
        if ($mapping->is_default) {
            $this->addError('delete', 'No se puede eliminar la regla por defecto');

            return;
        }

        $mapping->delete();
        $this->loadRules();
        $this->showSuccessMessage('Regla eliminada correctamente');
    }

    private function showSuccessMessage(string $message): void
    {
        $this->showSuccess = true;
        $this->successMessage = $message;
    }

    public function render(): View
    {
        return view('livewire.accounting.config.accounting-rule-config-panel');
    }
}
