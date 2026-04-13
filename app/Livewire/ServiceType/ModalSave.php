<?php

namespace App\Livewire\ServiceType;

use App\Models\ServiceType;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal = false;

    public $service_type_id;

    public $title = 'Crear Tipo de Servicio';

    public $buttonSaveTitle = 'Guardar Tipo de Servicio';

    public $name = '';

    public $description = '';

    public $base_cost = 0;

    public $currency = 'USD';

    public $status = 'active';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|in:active,inactive',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'base_cost.required' => 'El costo base es obligatorio.',
        'base_cost.numeric' => 'El costo debe ser un número.',
        'base_cost.min' => 'El costo debe ser mayor o igual a 0.',
    ];

    public function render()
    {
        return view('livewire.service-type.modal-save');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('openServiceTypeModal')]
    public function openModal($title): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->title = $title;
    }

    public function resetForm(): void
    {
        $this->service_type_id = null;
        $this->name = '';
        $this->description = '';
        $this->base_cost = 0;
        $this->currency = 'USD';
        $this->status = 'active';
        $this->buttonSaveTitle = 'Guardar Tipo de Servicio';
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'base_cost' => $this->base_cost,
                'currency' => $this->currency,
                'status' => $this->status,
                'updated_by' => auth()->id(),
            ];

            if ($this->service_type_id) {
                $serviceType = ServiceType::findOrFail($this->service_type_id);
                $serviceType->update($data);

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Actualizado exitosamente!'
                );
            } else {
                $data['fhir_id'] = 'service-type-'.Str::uuid();
                $data['created_by'] = auth()->id();

                ServiceType::create($data);

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Creado exitosamente!'
                );
            }

            $this->closeModal();
            $this->dispatch('refreshServiceTypes');

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al guardar: '.$e->getMessage()
            );
        }
    }

    #[On('editServiceTypeModal')]
    public function edit($service_type_id): void
    {
        $serviceType = ServiceType::find($service_type_id);

        if ($serviceType) {
            $this->service_type_id = $service_type_id;
            $this->title = 'Actualizar Tipo de Servicio';
            $this->buttonSaveTitle = 'Actualizar Tipo de Servicio';

            $this->name = $serviceType->name;
            $this->description = $serviceType->description;
            $this->base_cost = $serviceType->base_cost;
            $this->currency = $serviceType->currency;
            $this->status = $serviceType->status;

            $this->showModal = true;
        }
    }
}
