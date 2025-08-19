<?php

namespace App\Livewire\Patient;

use App\Models\InsuranceCompany;
use App\Models\Patient;
use App\Models\PatientInsurancePolicy;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddInsurance extends Component
{
    public $patient_id;

    public $patient;

    public $showInsuranceModal = false;

    public $existingPolicies = [];

    // Insurance form fields
    #[Validate('required')]
    public $insurance_company_id;

    #[Validate('required')]
    public $policy_number;

    public $group_number;

    #[Validate('required')]
    public $subscriber_id;

    #[Validate('required')]
    public $subscriber_name;

    #[Validate('required')]
    public $relationship_to_subscriber = 'self';

    #[Validate('required|date')]
    public $effective_date;

    #[Validate('nullable|date|after:effective_date')]
    public $expiration_date;

    #[Validate('required|in:primary,secondary,tertiary')]
    public $priority = 'primary';

    #[Validate('nullable|numeric|min:0|max:100')]
    public $coverage_percentage;

    #[Validate('nullable|numeric|min:0')]
    public $copay_amount;

    #[Validate('nullable|numeric|min:0')]
    public $deductible_amount;

    #[Validate('nullable|numeric|min:0')]
    public $out_of_pocket_max;

    public $is_active = true;

    public $notes;

    // Insurance companies
    public $insuranceCompanies = [];

    protected $messages = [
        'insurance_company_id.required' => 'La compañía de seguros es obligatoria.',
        'policy_number.required' => 'El número de póliza es obligatorio.',
        'subscriber_id.required' => 'El ID del titular es obligatorio.',
        'subscriber_name.required' => 'El nombre del titular es obligatorio.',
        'relationship_to_subscriber.required' => 'La relación con el titular es obligatoria.',
        'effective_date.required' => 'La fecha de inicio es obligatoria.',
        'effective_date.date' => 'La fecha de inicio debe ser una fecha válida.',
        'expiration_date.date' => 'La fecha de vencimiento debe ser una fecha válida.',
        'expiration_date.after' => 'La fecha de vencimiento debe ser posterior a la fecha de inicio.',
        'priority.required' => 'La prioridad del seguro es obligatoria.',
        'priority.in' => 'La prioridad debe ser primario, secundario o terciario.',
        'coverage_percentage.numeric' => 'El porcentaje de cobertura debe ser un número.',
        'coverage_percentage.min' => 'El porcentaje de cobertura debe ser mayor a 0.',
        'coverage_percentage.max' => 'El porcentaje de cobertura no puede ser mayor a 100.',
        'copay_amount.numeric' => 'El copago debe ser un número.',
        'copay_amount.min' => 'El copago debe ser mayor a 0.',
        'deductible_amount.numeric' => 'El deducible debe ser un número.',
        'deductible_amount.min' => 'El deducible debe ser mayor a 0.',
        'out_of_pocket_max.numeric' => 'El máximo de bolsillo debe ser un número.',
        'out_of_pocket_max.min' => 'El máximo de bolsillo debe ser mayor a 0.',
    ];

    public function mount($patient_id, $showInsuranceModal = false, $hideButton = false)
    {
        $this->patient_id = $patient_id;
        $this->patient = Patient::find($patient_id);
        $this->effective_date = Carbon::now()->format('Y-m-d');
        $this->showInsuranceModal = $showInsuranceModal;
        $this->loadInsuranceCompanies();

        if ($showInsuranceModal) {
            $this->loadExistingPolicies();
        }
    }

    public function render()
    {
        return view('livewire.patient.add-insurance');
    }

    public function loadInsuranceCompanies()
    {
        $this->insuranceCompanies = InsuranceCompany::active()
            ->whereClientId(auth()->user()->getCurrentClient()->id)
            ->orderBy('name')
            ->get();
    }

    public function loadExistingPolicies()
    {
        $this->existingPolicies = PatientInsurancePolicy::where('patient_id', $this->patient_id)
            ->with('insuranceCompany')
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function openModal()
    {
        $this->showInsuranceModal = true;
        $this->resetForm();
        $this->loadExistingPolicies();
    }

    public function closeModal()
    {
        $this->showInsuranceModal = false;
        $this->resetForm();

        // Emit event to parent component
        $this->dispatch('insurance-modal-closed');
    }

    public function updatedInsuranceCompanyId()
    {

        if ($this->insurance_company_id) {
            $company = InsuranceCompany::find($this->insurance_company_id);
            if ($company) {
                // Set default values from insurance company
                $this->coverage_percentage = $company->default_coverage_percentage ?? 80;
                $this->copay_amount = $company->default_copay_amount ?? 0;

            }
        }
    }

    public function updatedPriority()
    {
        // Check if there's already a policy with this priority for this patient
        $existingPolicy = PatientInsurancePolicy::where('patient_id', $this->patient_id)
            ->where('priority', $this->priority)
            ->where('is_active', true)
            ->first();

        if ($existingPolicy) {
            session()->flash('warning', "Ya existe un seguro {$this->priority} activo para este paciente. Al guardar este seguro, el anterior se marcará como inactivo.");
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Check for existing policy with same priority
            $existingPolicy = PatientInsurancePolicy::where('patient_id', $this->patient_id)
                ->where('priority', $this->priority)
                ->where('is_active', true)
                ->first();

            // If there's an existing policy with same priority, deactivate it
            if ($existingPolicy) {
                $existingPolicy->update(['is_active' => false]);
            }
            // Create new insurance policy

            $insurancePolicy = PatientInsurancePolicy::create([
                'patient_id' => $this->patient_id,
                'insurance_company_id' => $this->insurance_company_id,
                'policy_number' => $this->policy_number,
                'group_number' => $this->group_number,
                'subscriber_id' => $this->subscriber_id,
                'subscriber_name' => $this->subscriber_name,
                'relationship_to_subscriber' => $this->relationship_to_subscriber,
                'effective_date' => $this->effective_date,
                'expiration_date' => $this->expiration_date,
                'priority' => $this->priority,
                'coverage_percentage' => $this->coverage_percentage,
                'copay_amount' => $this->copay_amount,
                'deductible_amount' => $this->deductible_amount,
                'deductible_remaining' => $this->deductible_amount,
                'out_of_pocket_max' => $this->out_of_pocket_max,
                'out_of_pocket_remaining' => $this->out_of_pocket_max,
                'is_active' => $this->is_active,
                'notes' => $this->notes,
            ]);

            // Reload existing policies to update the table
            $this->loadExistingPolicies();

            // Reset form but keep modal open to show updated list
            $this->resetForm();

            // Emit event to refresh parent component
            $this->dispatch('insurance-added');

            $this->dispatch('showToastr',
                type: 'success',
                message: 'Seguro agregado exitosamente. ',
            );

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al agregar el seguro: '.$e->getMessage(),
            );

        }
    }

    private function resetForm()
    {
        $this->insurance_company_id = '';
        $this->policy_number = '';
        $this->group_number = '';
        $this->subscriber_id = '';
        $this->subscriber_name = '';
        $this->relationship_to_subscriber = 'self';
        $this->effective_date = Carbon::now()->format('Y-m-d');
        $this->expiration_date = '';
        $this->priority = 'primary';
        $this->coverage_percentage = '';
        $this->copay_amount = '';
        $this->deductible_amount = '';
        $this->out_of_pocket_max = '';
        $this->is_active = true;
        $this->notes = '';

        $this->resetErrorBag();
    }

    public function getRelationshipOptions()
    {
        return [
            'self' => 'Titular',
            'spouse' => 'Cónyuge',
            'child' => 'Hijo/a',
            'parent' => 'Padre/Madre',
            'sibling' => 'Hermano/a',
            'other' => 'Otro',
        ];
    }

    public function getPriorityOptions()
    {
        return [
            'primary' => 'Primario',
            'secondary' => 'Secundario',
            'tertiary' => 'Terciario',
        ];
    }

    public function togglePolicyStatus($policyId)
    {
        try {
            $policy = PatientInsurancePolicy::find($policyId);
            if ($policy && $policy->patient_id == $this->patient_id) {
                $policy->update(['is_active' => ! $policy->is_active]);
                $this->loadExistingPolicies();

                $status = $policy->is_active ? 'activado' : 'desactivado';
                session()->flash('message.success', "Seguro {$status} exitosamente.");
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al cambiar el estado del seguro: '.$e->getMessage());
        }
    }

    public function deletePolicy($policyId)
    {
        try {
            $policy = PatientInsurancePolicy::find($policyId);
            if ($policy && $policy->patient_id == $this->patient_id) {
                $policy->delete();
                $this->loadExistingPolicies();
                session()->flash('message.success', 'Seguro eliminado exitosamente.');
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al eliminar el seguro: '.$e->getMessage());
        }
    }
}
