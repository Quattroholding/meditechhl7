<?php

namespace App\Livewire\Medicine;

use App\Models\Medicine;
use Livewire\Component;

class CreateForm extends Component
{
    public $generic_name = '';

    public $home_name = '';

    public $ndc_code = '';

    public $type = '';

    public $mgs = '';

    public $mgs_type = '';

    public $active = 1;

    public $narcotic = 0;

    protected $rules = [
        'generic_name' => 'required|string|max:255',
        'home_name' => 'nullable|string|max:255',
        'ndc_code' => ['nullable', 'string', 'regex:/^(\d{5}-\d{3}-\d{2}|\d{4}-\d{4}-\d{2}|\d{5}-\d{4}-\d{1})$/'],
        'type' => 'required|string|max:100',
        'mgs' => 'required|string|max:50',
        'mgs_type' => 'required|string|max:50',
        'active' => 'required',
        'narcotic' => 'required',
    ];

    protected $messages = [
        'generic_name.required' => 'El nombre genérico es obligatorio.',
        'type.required' => 'El tipo de medicamento es obligatorio.',
        'mgs.required' => 'La dosis es obligatoria.',
        'mgs_type.required' => 'El tipo de dosis es obligatorio.',
        'ndc_code.regex' => 'El código NDC debe tener el formato #####-###-##, ####-####-## o #####-####-#.',
    ];

    public function render()
    {
        return view('livewire.medicine.create-form');
    }

    public function saveMedicine()
    {
        $this->validate();

        try {
            $medicineData = [
                'generic_name' => $this->generic_name,
                'home_name' => $this->home_name,
                'ndc_code' => $this->ndc_code,
                'type' => $this->type,
                'mgs' => $this->mgs,
                'mgs_type' => $this->mgs_type,
                'narcotic' => $this->narcotic,
                'active' => $this->active,
                'client_id' => auth()->user()->getCurrentClient()->id,
                'user_id' => auth()->user()->id,
                'source' => 'CUSTOM',
            ];

            Medicine::create($medicineData);

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
