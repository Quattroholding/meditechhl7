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

            // Create the primary ingredient
            $medication->ingredients()->create([
                'substance_display' => $this->generic_name,
                'strength_value' => $this->strength_value,
                'strength_unit' => $this->strength_unit,
            ]);

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
