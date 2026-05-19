<?php

namespace App\Livewire\Medicine;

use App\Models\Medication;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class EditForm extends Component
{
    public $medication_id;

    public $medication;

    public $generic_name = '';

    public $home_name = '';

    public $code = '';

    public $form = '';

    public $manufacturer = '';

    public $status = 'active';

    public $ingredients = [];

    public function mount($medication_id)
    {
        $this->medication_id = $medication_id;
        $this->medication = Medication::with('ingredients')->findOrFail($medication_id);

        // Load medication data
        $this->generic_name = $this->medication->generic_name;
        $this->home_name = $this->medication->home_name;
        $this->code = $this->medication->code;
        $this->form = $this->medication->form;
        $this->manufacturer = $this->medication->manufacturer;
        $this->status = $this->medication->status;

        // Load ingredients
        $this->ingredients = $this->medication->ingredients->map(function ($ing) {
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
    }

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
        return view('livewire.medicine.edit-form');
    }

    public function updateMedicine()
    {
        $this->validate();

        try {

            // Authorize using the policy and get the response message
            $response = Gate::inspect('update', $this->medication);

            if ($response->denied()) {

                session()->flash('message.error', $response->message());

                return redirect()->route('medicine.index');
            }
            // Update medication
            $this->medication->update([
                'generic_name' => $this->generic_name,
                'home_name' => $this->home_name,
                'display' => $this->home_name ?: $this->generic_name,
                'code' => $this->code,
                'form' => $this->form,
                'manufacturer' => $this->manufacturer,
                'status' => $this->status,
                'is_brand' => ! empty($this->home_name) && $this->home_name !== $this->generic_name,
            ]);

            // Get existing ingredient IDs
            $existingIds = $this->medication->ingredients()->pluck('id')->toArray();
            $updatedIds = [];

            // Update or create ingredients
            foreach ($this->ingredients as $ingredientData) {
                if (! empty($ingredientData['id'])) {
                    // Update existing ingredient
                    $this->medication->ingredients()
                        ->where('id', $ingredientData['id'])
                        ->update([
                            'substance_display' => $ingredientData['substance_display'],
                            'strength_value' => $ingredientData['strength_value'],
                            'strength_unit' => $ingredientData['strength_unit'],
                        ]);
                    $updatedIds[] = $ingredientData['id'];
                } else {
                    // Create new ingredient
                    $newIngredient = $this->medication->ingredients()->create([
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
                $this->medication->ingredients()->whereIn('id', $toDelete)->delete();
            }

            session()->flash('message.success', 'Medicamento actualizado exitosamente.');

            return redirect()->route('medicine.index');

        } catch (\Exception $e) {
            $this->dispatch('showToastrUpdateMEdication',
                type: 'error',
                message: 'Error al actualizar el medicamento: '.$e->getMessage(),
            );
            session()->flash('message.error', 'Error al actualizar el medicamento: '.$e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('medicine.index');
    }
}
