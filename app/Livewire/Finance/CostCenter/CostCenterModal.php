<?php

namespace App\Livewire\Finance\CostCenter;

use App\Models\Branch;
use App\Models\Finance\CostCenter;
use App\Models\MedicalSpeciality;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CostCenterModal extends Component
{
    public bool $isModal = true;

    public ?CostCenter $costCenter = null;

    #[Validate]
    public string $code = '';

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $description = '';

    #[Validate]
    public ?int $branch_id = null;

    #[Validate]
    public ?int $medical_speciality_id = null;

    #[Validate]
    public string $status = 'active';

    public function mount(?CostCenter $costCenter = null): void
    {
        if ($costCenter) {
            $this->costCenter = $costCenter;
            $this->code = $costCenter->code;
            $this->name = $costCenter->name;
            $this->description = $costCenter->description ?? '';
            $this->branch_id = $costCenter->branch_id;
            $this->medical_speciality_id = $costCenter->medical_speciality_id;
            $this->status = $costCenter->status;
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
                Rule::unique('cost_centers', 'code')
                    ->where('client_id', $clientId)
                    ->ignore($this->costCenter?->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cost_centers', 'name')
                    ->where('client_id', $clientId)
                    ->ignore($this->costCenter?->id),
            ],
            'description' => 'nullable|string|max:1000',
            'branch_id' => 'required|exists:branches,id',
            'medical_speciality_id' => 'nullable|exists:medical_specialities,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save(): void
    {
        $this->authorize('finance.cost-centers.manage');
        $this->validate();

        $clientId = auth()->user()->getCurrentClient()->id;

        if ($this->costCenter) {
            $this->costCenter->update([
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'branch_id' => $this->branch_id,
                'medical_speciality_id' => $this->medical_speciality_id,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ]);
        } else {
            CostCenter::create([
                'client_id' => $clientId,
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'branch_id' => $this->branch_id,
                'medical_speciality_id' => $this->medical_speciality_id,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ]);
        }

        $this->dispatch('cost-center-saved');
        $this->dispatch('closeModal');
    }

    public function delete(): void
    {
        if ($this->costCenter) {
            $this->authorize('finance.cost-centers.manage');
            $this->costCenter->delete();
            $this->dispatch('cost-center-saved');
            $this->dispatch('closeModal');
        }
    }

    public function getBranchesProperty()
    {
        return Branch::where('client_id', auth()->user()->getCurrentClient()->id)
            ->orderBy('name')
            ->get();
    }

    public function getSpecialitiesProperty()
    {
        return MedicalSpeciality::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.finance.cost-center.cost-center-modal', [
            'branches' => $this->branches,
            'specialities' => $this->specialities,
        ]);
    }
}
