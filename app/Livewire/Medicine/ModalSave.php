<?php

namespace App\Livewire\Medicine;

use App\Models\Medication;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal;

    public $medication;

    public $title;

    public $buttonSaveTitle = 'Guardar Medicamento';

    public $generic_name = '';

    public $home_name = '';

    public $code = '';

    public $form = '';

    public $strength_value = '';

    public $strength_unit = '';

    public $manufacturer = '';

    public $status = 'active';

    protected $rules = [
        'generic_name' => 'required|string|max:255',
        'home_name' => 'nullable|string|max:255',
        'code' => 'nullable|string|max:100',
        'form' => 'required|string|max:100',
        'strength_value' => 'required|numeric',
        'strength_unit' => 'required|string|max:50',
        'manufacturer' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'generic_name.required' => 'El nombre genérico es obligatorio.',
        'form.required' => 'El tipo de medicamento es obligatorio.',
        'strength_value.required' => 'La dosis es obligatoria.',
        'strength_value.numeric' => 'La dosis debe ser un número.',
        'strength_unit.required' => 'La unidad de dosis es obligatoria.',
    ];

    public function render()
    {
        return view('livewire.medicine.modal-save');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('openMedicineModal')]
    public function openModal($title)
    {
        $this->resetForm();
        $this->showModal = true;
        $this->title = $title;
    }

    public function resetForm()
    {
        $this->medication = null;
        $this->generic_name = '';
        $this->home_name = '';
        $this->code = '';
        $this->form = '';
        $this->strength_value = '';
        $this->strength_unit = '';
        $this->manufacturer = '';
        $this->status = 'active';
        $this->buttonSaveTitle = 'Guardar Medicamento';
    }

    public function saveMedicine()
    {
        $this->validate();

        try {
            $medicationData = [
                'fhir_id' => (string) Str::uuid(),
                'generic_name' => $this->generic_name,
                'home_name' => $this->home_name,
                'display' => $this->home_name ?: $this->generic_name,
                'code' => $this->code,
                'code_system' => 'CUSTOM',
                'form' => $this->form,
                'manufacturer' => $this->manufacturer,
                'status' => $this->status,
                'is_brand' => ! empty($this->home_name) && $this->home_name !== $this->generic_name,
            ];

            if ($this->medication) {
                // Update existing medication
                unset($medicationData['fhir_id']);
                $this->medication->update($medicationData);

                // Update or create the primary ingredient
                $ingredient = $this->medication->ingredients()->first();
                if ($ingredient) {
                    $ingredient->update([
                        'substance_display' => $this->generic_name,
                        'strength_value' => $this->strength_value,
                        'strength_unit' => $this->strength_unit,
                    ]);
                } else {
                    $this->medication->ingredients()->create([
                        'substance_display' => $this->generic_name,
                        'strength_value' => $this->strength_value,
                        'strength_unit' => $this->strength_unit,
                    ]);
                }

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Actualizado exitosamente!'
                );
            } else {
                // Create new medication
                $medication = Medication::create($medicationData);

                // Create the primary ingredient
                $medication->ingredients()->create([
                    'substance_display' => $this->generic_name,
                    'strength_value' => $this->strength_value,
                    'strength_unit' => $this->strength_unit,
                ]);

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Creado exitosamente!'
                );
            }

            $this->closeModal();
            $this->dispatch('refreshMedicines');

        } catch (\Exception $e) {
            $this->closeModal();
            session()->flash('message.error', 'Error al guardar el medicamento: '.$e->getMessage());
        }
    }

    #[On('editMedicineModal')]
    public function editMedicine($medication_id)
    {
        $this->medication = Medication::with('ingredients')->find($medication_id);
        $this->title = 'Actualizar Medicamento';
        $this->buttonSaveTitle = 'Actualizar Medicamento';

        if ($this->medication) {
            $this->generic_name = $this->medication->generic_name;
            $this->home_name = $this->medication->home_name;
            $this->code = $this->medication->code;
            $this->form = $this->medication->form;
            $this->manufacturer = $this->medication->manufacturer;
            $this->status = $this->medication->status;

            // Load primary ingredient data
            $ingredient = $this->medication->ingredients->first();
            if ($ingredient) {
                $this->strength_value = $ingredient->strength_value;
                $this->strength_unit = $ingredient->strength_unit;
            }

            $this->showModal = true;
        }
    }

    public function deleteMedicine($medicationId)
    {
        try {
            $medication = Medication::find($medicationId);
            if ($medication) {
                $medication->delete();
                session()->flash('message.success', 'Medicamento eliminado exitosamente.');
                $this->dispatch('refreshMedicines');
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al eliminar el medicamento.');
        }
    }
}
