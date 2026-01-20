<?php

namespace App\Livewire\Medicine;

use App\Models\Medication;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateForm extends Component
{
    public $generic_name = '';

    public $home_name = '';

    public $code = '';

    public $form = '';

    public $manufacturer = '';

    public $status = 'active';

    public $ingredients = [];

    public function mount()
    {
        // Initialize with one empty ingredient
        $this->ingredients = [
            ['substance_display' => '', 'strength_value' => '', 'strength_unit' => ''],
        ];
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
        $this->ingredients[] = ['substance_display' => '', 'strength_value' => '', 'strength_unit' => ''];
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
        return view('livewire.medicine.create-form');
    }

    public function saveMedicine()
    {
        $this->validate();

        try {
            $medication = Medication::create([
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
            ]);

            // Create all ingredients
            foreach ($this->ingredients as $ingredient) {
                $medication->ingredients()->create([
                    'substance_display' => $ingredient['substance_display'],
                    'strength_value' => $ingredient['strength_value'],
                    'strength_unit' => $ingredient['strength_unit'],
                ]);
            }

            session()->flash('message.success', 'Medicamento creado exitosamente.');

            return redirect()->route('medicine.index');

        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al guardar el medicamento: '.$e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('medicine.index');
    }
}
