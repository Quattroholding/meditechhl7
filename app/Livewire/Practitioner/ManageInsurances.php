<?php

namespace App\Livewire\Practitioner;

use App\Models\InsuranceCompany;
use App\Models\Practitioner;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManageInsurances extends Component
{
    public $practitioner_id;

    public $practitioner;

    public $showInsuranceModal = false;

    public $showBigButton = true;

    public $showSmallButton = false;

    public $existingInsurances = [];

    public $isEditing = false;

    // Insurance form fields
    #[Validate('required')]
    public $insurance_company_id;

    #[Validate('required|boolean')]
    public $accepts = true;

    #[Validate('nullable|numeric|min:0|max:100')]
    public $custom_coverage_percentage;

    #[Validate('nullable|numeric|min:0')]
    public $custom_copay_amount;

    public $notes;

    // Insurance companies
    public $insuranceCompanies = [];

    protected $messages = [
        'insurance_company_id.required' => 'La compañía de seguros es obligatoria.',
        'accepts.required' => 'Debe especificar si acepta o no el seguro.',
        'accepts.boolean' => 'El campo acepta debe ser verdadero o falso.',
        'custom_coverage_percentage.numeric' => 'El porcentaje de cobertura debe ser un número.',
        'custom_coverage_percentage.min' => 'El porcentaje de cobertura debe ser mayor a 0.',
        'custom_coverage_percentage.max' => 'El porcentaje de cobertura no puede ser mayor a 100.',
        'custom_copay_amount.numeric' => 'El copago debe ser un número.',
        'custom_copay_amount.min' => 'El copago debe ser mayor a 0.',
    ];

    public function mount($practitioner_id, $showInsuranceModal = false)
    {
        $this->practitioner_id = $practitioner_id;
        $this->practitioner = Practitioner::find($practitioner_id);
        $this->showInsuranceModal = $showInsuranceModal;
        $this->loadInsuranceCompanies();

        if ($showInsuranceModal) {
            $this->loadExistingInsurances();
        }
    }

    public function render()
    {
        return view('livewire.practitioner.manage-insurances');
    }

    public function loadInsuranceCompanies()
    {
        $this->insuranceCompanies = InsuranceCompany::active()
            ->whereClientId(auth()->user()->getCurrentClient()->id)
            ->orderBy('name')
            ->get();
    }

    public function loadExistingInsurances()
    {
        $this->existingInsurances = $this->practitioner->insuranceCompanies()
            ->withPivot('accepts', 'custom_coverage_percentage', 'custom_copay_amount', 'notes', 'created_at')
            ->orderBy('name')
            ->get();
    }

    public function openModal()
    {
        $this->showInsuranceModal = true;
        $this->resetForm();
        $this->loadExistingInsurances();
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
        // Don't override values when editing
        if ($this->isEditing) {
            return;
        }

        if ($this->insurance_company_id) {
            $company = InsuranceCompany::find($this->insurance_company_id);
            if ($company) {
                // Set default values from insurance company only if fields are empty
                if (empty($this->custom_coverage_percentage)) {
                    $this->custom_coverage_percentage = $company->default_coverage_percentage ?? 80;
                }
                if (empty($this->custom_copay_amount)) {
                    $this->custom_copay_amount = $company->default_copay_amount ?? 0;
                }
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Check if relationship already exists
            $existingRelation = $this->practitioner->insuranceCompanies()
                ->where('insurance_company_id', $this->insurance_company_id)
                ->first();

            if ($existingRelation) {
                // Update existing relationship
                $this->practitioner->insuranceCompanies()->updateExistingPivot($this->insurance_company_id, [
                    'accepts' => $this->accepts,
                    'custom_coverage_percentage' => $this->custom_coverage_percentage,
                    'custom_copay_amount' => $this->custom_copay_amount,
                    'notes' => $this->notes,
                    'updated_at' => now(),
                ]);

                $message = 'Relación con seguro actualizada exitosamente.';
            } else {
                // Create new relationship
                $this->practitioner->insuranceCompanies()->attach($this->insurance_company_id, [
                    'accepts' => $this->accepts,
                    'custom_coverage_percentage' => $this->custom_coverage_percentage,
                    'custom_copay_amount' => $this->custom_copay_amount,
                    'notes' => $this->notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $message = 'Relación con seguro agregada exitosamente.';
            }

            // Reload existing insurances to update the table
            $this->loadExistingInsurances();

            // Reset form but keep modal open to show updated list
            $this->resetForm();

            // Emit event to refresh parent component
            $this->dispatch('insurance-relationship-saved'.$this->practitioner_id);

            $this->dispatch('showToastrMI'.$this->practitioner_id,
                type: 'success',
                message: $message,
            );

        } catch (\Exception $e) {
            $this->dispatch('showToastrMI'.$this->practitioner_id,
                type: 'error',
                message: 'Error al guardar la relación: '.$e->getMessage(),
            );
        }
    }

    private function resetForm()
    {
        $this->isEditing = false;
        $this->insurance_company_id = '';
        $this->accepts = true;
        $this->custom_coverage_percentage = '';
        $this->custom_copay_amount = '';
        $this->notes = '';

        $this->resetErrorBag();
    }

    public function editInsurance($insuranceCompanyId)
    {
        $insurance = $this->practitioner->insuranceCompanies()
            ->where('insurance_company_id', $insuranceCompanyId)
            ->first();

        if ($insurance) {
            $this->isEditing = true;
            $this->insurance_company_id = $insuranceCompanyId;
            $this->accepts = $insurance->pivot->accepts;
            $this->custom_coverage_percentage = $insurance->pivot->custom_coverage_percentage;
            $this->custom_copay_amount = $insurance->pivot->custom_copay_amount;
            $this->notes = $insurance->pivot->notes;
        }
    }

    public function deleteInsurance($insuranceCompanyId)
    {
        try {
            $this->practitioner->insuranceCompanies()->detach($insuranceCompanyId);
            $this->loadExistingInsurances();

            $this->dispatch('showToastrMI'.$this->practitioner_id,
                type: 'success',
                message: 'Relación con seguro eliminada exitosamente.',
            );
        } catch (\Exception $e) {
            $this->dispatch('showToastrMI'.$this->practitioner_id,
                type: 'error',
                message: 'Error al eliminar la relación: '.$e->getMessage(),
            );
        }
    }

    public function getAcceptanceOptions()
    {
        return [
            1 => 'Acepta',
            0 => 'No Acepta',
        ];
    }
}
