<?php

namespace App\Livewire\Medicine;

use App\Models\Medicine;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal;
    public $medicine;
    public $title;
    public $buttonSaveTitle = 'Guardar Medicamento';

    public $generic_name = '';
    public $home_name = '';
    public $ndc_code = '';
    public $type = '';
    public $mgs = '';
    public $mgs_type = '';
    public $instructions = '';
    public $side_effects = '';
    public $contraindications = '';
    public $interactions = '';
    public $active = 1;
    public $narcotic = 1;

    protected $rules = [
        'generic_name' => 'required|string|max:255',
        'home_name' => 'nullable|string|max:255',
        'ndc_code' => ['nullable', 'string', 'regex:/^(\d{5}-\d{3}-\d{2}|\d{4}-\d{4}-\d{2}|\d{5}-\d{4}-\d{1})$/'],
        'type' => 'required|string|max:100',
        'mgs' => 'required|string|max:50',
        'mgs_type' => 'required|string|max:50',
        /*
        'instructions' => 'nullable|string',
        'side_effects' => 'nullable|string',
        'contraindications' => 'nullable|string',
        'interactions' => 'nullable|string',
        */
        'active' => 'required'
    ];

    protected $messages = [
        'generic_name.required' => 'El nombre genérico es obligatorio.',
        'type.required' => 'El tipo de medicamento es obligatorio.',
        'mgs.required' => 'La dosis es obligatoria.',
        'mgs_type.required' => 'El tipo de dosis es obligatorio.',
        'ndc_code.regex' => 'El código NDC debe tener el formato #####-###-##, ####-####-## o #####-####-#.'
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
        $this->medicine = null;
        $this->generic_name = '';
        $this->home_name = '';
        $this->ndc_code = '';
        $this->type = '';
        $this->mgs = '';
        $this->mgs_type = '';
        $this->instructions = '';
        $this->side_effects = '';
        $this->contraindications = '';
        $this->interactions = '';
        $this->active = 1;
        $this->narcotic = 0;
        $this->buttonSaveTitle = 'Guardar Medicamento';
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
                /*
                'instructions' => $this->instructions,
                'side_effects' => $this->side_effects,
                'contraindications' => $this->contraindications,
                'interactions' => $this->interactions,
                */
                'narcotic' => $this->narcotic,
                'active' => $this->active,
                'client_id' => auth()->user()->getCurrentClient()->id,
                'user_id' => auth()->user()->id
            ];

            if ($this->medicine) {
                $this->medicine->update($medicineData);

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Actualizado exitosamente!'
                );
            } else {
                Medicine::create($medicineData);
                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Creado exitosamente!'
                );
            }

            $this->closeModal();
            $this->dispatch('refreshMedicines');

        } catch (\Exception $e) {
            $this->closeModal();
            session()->flash('message.error', 'Error al guardar el medicamento: ' . $e->getMessage());
        }
    }

    #[On('editMedicineModal')]
    public function editMedicine($medicine_id)
    {
        $this->medicine = Medicine::find($medicine_id);
        $this->title = 'Actualizar Medicamento';
        $this->buttonSaveTitle = 'Actualizar Medicamento';

        if ($this->medicine) {
            $this->generic_name = $this->medicine->generic_name;
            $this->home_name = $this->medicine->home_name;
            $this->ndc_code = $this->medicine->ndc_code;
            $this->type = $this->medicine->type;
            $this->mgs = $this->medicine->mgs;
            $this->mgs_type = $this->medicine->mgs_type;
            /*
            $this->instructions = $this->medicine->instructions;
            $this->side_effects = $this->medicine->side_effects;
            $this->contraindications = $this->medicine->contraindications;
            $this->interactions = $this->medicine->interactions;
            */
            $this->narcotic = $this->medicine->narcotic;
            $this->active = $this->medicine->active;
            $this->showModal = true;
        }
    }

    public function deleteMedicine($medicineId)
    {
        try {
            $medicine = Medicine::find($medicineId);
            if ($medicine) {
                $medicine->delete();
                session()->flash('message.success', 'Medicamento eliminado exitosamente.');
                $this->dispatch('refreshMedicines');
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al eliminar el medicamento.');
        }
    }
}
