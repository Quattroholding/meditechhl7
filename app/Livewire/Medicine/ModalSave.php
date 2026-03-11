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

    public $medication_id;

    public $title;

    public $buttonSaveTitle = 'Guardar Medicamento';

    public $generic_name = '';

    public $home_name = '';

    public $code = '';

    public $form = '';

    public $manufacturer = '';

    public $status = 'active';

    public $ingredients = [];

    protected function rules()
    {
        return [
            'generic_name' => 'required|string|max:255',
            'home_name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100',
            'form' => 'required|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.substance_display' => 'required|string|max:255',
            'ingredients.*.strength_value' => 'required|numeric',
            'ingredients.*.strength_unit' => 'required|string|max:50',
        ];
    }

    protected $messages = [
        'generic_name.required' => 'El nombre genérico es obligatorio.',
        'form.required' => 'La forma farmacéutica es obligatoria.',
        'ingredients.required' => 'Debe agregar al menos un ingrediente.',
        'ingredients.min' => 'Debe agregar al menos un ingrediente.',
        'ingredients.*.substance_display.required' => 'El nombre del ingrediente es obligatorio.',
        'ingredients.*.strength_value.required' => 'La dosis es obligatoria.',
        'ingredients.*.strength_value.numeric' => 'La dosis debe ser un número.',
        'ingredients.*.strength_unit.required' => 'La unidad es obligatoria.',
    ];

    public function addIngredient()
    {
        $this->ingredients[] = ['id' => null, 'substance_display' => '', 'strength_value' => '', 'strength_unit' => ''];
    }

    public function removeIngredient($index)
    {
        if (count($this->ingredients) > 1) {
            unset($this->ingredients[$index]);
            $this->ingredients = array_values($this->ingredients);
        }
    }

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
        $this->medication_id = null;
        $this->generic_name = '';
        $this->home_name = '';
        $this->code = '';
        $this->form = '';
        $this->manufacturer = '';
        $this->status = 'active';
        $this->ingredients = [
            ['id' => null, 'substance_display' => '', 'strength_value' => '', 'strength_unit' => ''],
        ];
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

            if ($this->medication_id) {
                // Update existing medication
                unset($medicationData['fhir_id']);
                $medication = Medication::findOrFail($this->medication_id);
                $medication->update($medicationData);

                // Get existing ingredient IDs
                $existingIds = $medication->ingredients()->pluck('id')->toArray();
                $updatedIds = [];

                // Update or create ingredients
                foreach ($this->ingredients as $ingredientData) {
                    if (! empty($ingredientData['id'])) {
                        // Update existing ingredient
                        $medication->ingredients()
                            ->where('id', $ingredientData['id'])
                            ->update([
                                'substance_display' => $ingredientData['substance_display'],
                                'strength_value' => $ingredientData['strength_value'],
                                'strength_unit' => $ingredientData['strength_unit'],
                            ]);
                        $updatedIds[] = $ingredientData['id'];
                    } else {
                        // Create new ingredient
                        $newIngredient = $medication->ingredients()->create([
                            'substance_display' => $ingredientData['substance_display'],
                            'strength_value' => $ingredientData['strength_value'],
                            'strength_unit' => $ingredientData['strength_unit'],
                        ]);
                        $updatedIds[] = $newIngredient->id;
                    }
                }

                // Delete removed ingredients
                $toDelete = array_diff($existingIds, $updatedIds);
                if (! empty($toDelete)) {
                    $medication->ingredients()->whereIn('id', $toDelete)->delete();
                }

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Actualizado exitosamente!'
                );
            } else {
                // Create new medication
                $medication = Medication::create($medicationData);

                // Create all ingredients
                foreach ($this->ingredients as $ingredientData) {
                    $medication->ingredients()->create([
                        'substance_display' => $ingredientData['substance_display'],
                        'strength_value' => $ingredientData['strength_value'],
                        'strength_unit' => $ingredientData['strength_unit'],
                    ]);
                }

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Creado exitosamente!'
                );
            }

            $this->closeModal();
            $this->dispatch('refreshMedicines');

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al guardar el medicamento: '.$e->getMessage()
            );
        }
    }

    #[On('editMedicineModal')]
    public function editMedicine($medication_id)
    {
        $medication = Medication::with('ingredients')->find($medication_id);
        $this->medication_id = $medication_id;
        $this->title = 'Actualizar Medicamento';
        $this->buttonSaveTitle = 'Actualizar Medicamento';

        if ($medication) {
            $this->generic_name = $medication->generic_name;
            $this->home_name = $medication->home_name;
            $this->code = $medication->code;
            $this->form = $medication->form;
            $this->manufacturer = $medication->manufacturer;
            $this->status = $medication->status;

            // Load all ingredients
            $this->ingredients = $medication->ingredients->map(function ($ing) {
                return [
                    'id' => $ing->id,
                    'substance_display' => $ing->substance_display,
                    'strength_value' => $ing->strength_value,
                    'strength_unit' => $ing->strength_unit,
                ];
            })->toArray();

            // Ensure at least one ingredient row
            if (empty($this->ingredients)) {
                $this->ingredients = [
                    ['id' => null, 'substance_display' => '', 'strength_value' => '', 'strength_unit' => ''],
                ];
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
