<?php

namespace App\Livewire\Settings;

use App\Models\Accounting\AccountingAccount;
use App\Models\CptCode;
use App\Models\Finance\CostCenter;
use App\Models\UserProcedure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class UserProcedureCreate extends Component
{
    public $clientId;

    public $created = [];

    public $description;

    public $query;

    public $results = [];

    public $cpt_id;

    public $current_price_cpt;

    public $current_price;

    public $type;

    public $editingProcedureId = null;

    public $accountingAccountId = null;

    public $costCenterId = null;

    public function render()
    {
        if (auth()->user()->clients()->first()) {
            $this->clientId = auth()->user()->clients()->first()->id;
        }
        $this->created = UserProcedure::whereUserId(auth()->user()->id)->get();

        return view('livewire.settings.user-procedure-create');
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $http = app()->environment('local') ? Http::withoutVerifying() : Http::asForm();
        $response = $http->get(url('api/cpts/procedure'), [
            'dropdown' => true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    public function selectOption($option)
    {
        $this->cpt_id = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados
    }

    public function saveCpt()
    {

        $this->validate([
            'cpt_id' => 'required',
            'current_price_cpt' => 'required',
        ], [
            'cpt_id.required' => 'El procedimiento es obligatorio.',
            'current_price_cpt.required' => 'El precio es obligatorio.',
        ]);

        $cpt = CptCode::whereId($this->cpt_id)->first();

        UserProcedure::create([
            'user_id' => auth()->id(),
            'client_id' => $this->clientId,
            'code' => $cpt->code,
            'cpt_code' => $cpt->code,
            'current_price' => $this->current_price_cpt,
        ]);

        /*$this->dispatch('showToastr',
            type: 'success',
            message: '¡Guardado exitosamente!'
        );*/
        session()->flash('message.success', '¡Guardado exitosamente!');

        // Dispatch event to refresh setup reminders
        $this->dispatch('refreshSetupReminders');

        $this->created = UserProcedure::whereUserId(auth()->user()->id)->get();

        $this->reset(['cpt_id', 'current_price_cpt']);

    }

    public function saveCustom()
    {

        $this->validate([
            'description' => 'required',
            'type' => 'required',
            'current_price' => 'required',
        ], [
            'description.required' => 'La descripcion es obligatoria..',
            'type.required' => 'El tipo es obligatorio.',
            'current_price.required' => 'El precio es obligatorio.',
        ]);

        UserProcedure::create([
            'user_id' => auth()->id(),
            'client_id' => $this->clientId,
            'code' => strtoupper('MDT'.Str::random(7)),
            'description' => $this->description,
            'type' => $this->type,
            'current_price' => $this->current_price,
        ]);

        $this->dispatch('showToastr',
            type: 'success',
            message: '¡Guardado exitosamente!'
        );

        // Dispatch event to refresh setup reminders
        $this->dispatch('refreshSetupReminders');

        $this->created = UserProcedure::whereUserId(auth()->user()->id)->get();

        $this->reset(['description', 'type', 'current_price']);
    }

    public function delete($id)
    {
        $up = UserProcedure::find($id);
        $up->delete();

        $this->dispatch('showToastr',
            type: 'error',
            message: '¡Eliminado exitosamente!'
        );
    }

    public function openAccountingModal($procedureId)
    {
        $procedure = UserProcedure::find($procedureId);
        if ($procedure) {
            $this->editingProcedureId = $procedureId;
            $this->accountingAccountId = $procedure->accounting_account_id;
            $this->costCenterId = $procedure->cost_center_id;
        }
    }

    public function closeAccountingModal()
    {
        $this->editingProcedureId = null;
        $this->accountingAccountId = null;
        $this->costCenterId = null;
    }

    public function saveAccounting()
    {
        $this->validate([
            'accountingAccountId' => 'required|exists:accounting_accounts,id',
            'costCenterId' => 'nullable|exists:cost_centers,id',
        ]);

        $procedure = UserProcedure::find($this->editingProcedureId);
        if ($procedure) {
            $procedure->update([
                'accounting_account_id' => $this->accountingAccountId,
                'cost_center_id' => $this->costCenterId,
            ]);

            $this->dispatch('showToastr',
                type: 'success',
                message: '¡Cuenta contable asignada exitosamente!'
            );

            $this->closeAccountingModal();
            $this->created = UserProcedure::whereUserId(auth()->user()->id)->get();
        }
    }

    public function getAccountingAccountsProperty()
    {
        return AccountingAccount::where('client_id', $this->clientId)
            ->orderBy('code')
            ->get();
    }

    public function getCostCentersProperty()
    {
        return CostCenter::where('client_id', $this->clientId)
            ->orderBy('name')
            ->get();
    }
}
