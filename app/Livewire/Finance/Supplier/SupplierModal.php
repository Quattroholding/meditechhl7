<?php

namespace App\Livewire\Finance\Supplier;

use App\Models\AccountingAccount;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SupplierModal extends Component
{
    public bool $isModal = true;

    public ?Supplier $supplier = null;

    #[Validate]
    public string $ruc = '';

    #[Validate]
    public string $legal_name = '';

    #[Validate]
    public string $commercial_name = '';

    #[Validate]
    public string $address = '';

    #[Validate]
    public string $phone = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $contact_person = '';

    #[Validate]
    public int $credit_days = 0;

    #[Validate]
    public int $accounting_account_id = 0;

    #[Validate]
    public string $status = 'active';

    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier) {
            $this->supplier = $supplier;
            $this->ruc = $supplier->ruc;
            $this->legal_name = $supplier->legal_name;
            $this->commercial_name = $supplier->commercial_name ?? '';
            $this->address = $supplier->address ?? '';
            $this->phone = $supplier->phone ?? '';
            $this->email = $supplier->email ?? '';
            $this->contact_person = $supplier->contact_person ?? '';
            $this->credit_days = $supplier->credit_days;
            $this->accounting_account_id = $supplier->accounting_account_id;
            $this->status = $supplier->status;
        }
    }

    public function rules(): array
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        return [
            'ruc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('suppliers', 'ruc')
                    ->where('client_id', $clientId)
                    ->ignore($this->supplier?->id),
            ],
            'legal_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'legal_name')
                    ->where('client_id', $clientId)
                    ->ignore($this->supplier?->id),
            ],
            'commercial_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'credit_days' => 'required|integer|min:0|max:365',
            'accounting_account_id' => 'required|exists:accounting_accounts,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save(): void
    {
        $this->authorize('payables.suppliers.manage');
        $this->validate();

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->supplier) {
            $this->supplier->update([
                'ruc' => $this->ruc,
                'legal_name' => $this->legal_name,
                'commercial_name' => $this->commercial_name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'contact_person' => $this->contact_person,
                'credit_days' => $this->credit_days,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Proveedor actualizado exitosamente.');
        } else {
            Supplier::create([
                'uuid' => Str::uuid(),
                'client_id' => $clientId,
                'ruc' => $this->ruc,
                'legal_name' => $this->legal_name,
                'commercial_name' => $this->commercial_name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'contact_person' => $this->contact_person,
                'credit_days' => $this->credit_days,
                'accounting_account_id' => $this->accounting_account_id,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
            session()->flash('message.success', 'Proveedor creado exitosamente.');
        }

        if ($this->isModal) {
            $this->dispatch('supplier-saved');
            $this->dispatch('closeModal');
        } else {
            $this->redirect(route('finance.payables.suppliers'));
        }
    }

    public function delete(): void
    {
        if ($this->supplier) {
            $this->authorize('payables.suppliers.manage');
            $this->supplier->delete();
            session()->flash('message.success', 'Proveedor eliminado exitosamente.');

            if ($this->isModal) {
                $this->dispatch('supplier-saved');
                $this->dispatch('closeModal');
            } else {
                $this->redirect(route('finance.payables.suppliers'));
            }
        }
    }

    public function getAccountingAccountsProperty()
    {
        return AccountingAccount::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('code', '2101') // CxP
            ->orWhere('account_type', 'liability')
            ->get();
    }

    public function render()
    {
        return view('livewire.finance.supplier.supplier-modal', [
            'accounts' => $this->accountingAccounts,
        ]);
    }
}
