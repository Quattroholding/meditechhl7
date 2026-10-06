<?php

namespace App\Livewire\Finance\CostCenter;

use App\Models\Branch;
use App\Models\Finance\CostCenter;
use App\Models\MedicalSpeciality;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CostCenterModal extends Component
{
    public bool $isModal = true;

    public ?CostCenter $costCenter = null;

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public ?int $branch_id = null;

    public ?int $medical_speciality_id = null;

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

    public function updatedCode(): void
    {
        $this->validateOnly('code');
    }

    public function updatedName(): void
    {
        $this->validateOnly('name');
    }

    public function updatedBranchId(): void
    {
        $this->validateOnly('branch_id');
    }

    protected $rules = [
        'code' => 'required|string|max:50',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'branch_id' => 'required|exists:branches,id',
        'medical_speciality_id' => 'nullable|exists:medical_specialities,id',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'code.required' => 'El código es obligatorio.',
        'code.string' => 'El código debe ser texto.',
        'code.max' => 'El código no puede exceder 50 caracteres.',
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser texto.',
        'name.max' => 'El nombre no puede exceder 255 caracteres.',
        'description.string' => 'La descripción debe ser texto.',
        'description.max' => 'La descripción no puede exceder 1000 caracteres.',
        'branch_id.required' => 'La sucursal es obligatoria.',
        'branch_id.exists' => 'La sucursal seleccionada no existe.',
        'medical_speciality_id.exists' => 'La especialidad seleccionada no existe.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado debe ser activo o inactivo.',
    ];

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
        $this->validate();
        $this->authorize('cost-centers.create');

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
                'uuid' => Str::uuid(),
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

    public function getBranchesProperty(): Collection
    {
        return Branch::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('active', 1)
            ->orderBy('name')
            ->get();
    }

    public function getSpecialitiesProperty(): Collection
    {
        return MedicalSpeciality::orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.finance.cost-center.cost-center-modal', [
            'branches' => $this->branches,
            'specialities' => $this->specialities,
        ]);
    }
}
