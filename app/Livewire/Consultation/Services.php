<?php

namespace App\Livewire\Consultation;

use App\Models\ChargeItem;
use App\Models\Encounter;
use App\Models\ServiceCatalog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Services extends Component
{
    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $selectedServices = [];

    public $selectedChargeItems = [];

    // Service selection
    public $selectedServiceId;

    public $customQuantity = 1;

    public $customPrice = null;

    public $serviceNotes = '';

    public $rapidAccess = [];

    public $servicesCatalog;

    public $saved = false;

    protected $rules = [
        'customQuantity' => 'required|numeric|min:0.01',
        'customPrice' => 'nullable|numeric|min:0',
    ];

    protected $messages = [
        'customQuantity.required' => 'La cantidad es obligatoria.',
        'customQuantity.numeric' => 'La cantidad debe ser un número.',
        'customQuantity.min' => 'La cantidad debe ser mayor a 0.',
        'customPrice.numeric' => 'El precio debe ser un número válido.',
        'customPrice.min' => 'El precio debe ser mayor a 0.',
    ];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->loadSelectedServices();
        $this->loadRapidAccess();
    }

    public function loadSelectedServices()
    {
        // Cargar ChargeItems ya asociados a este encounter
        $this->selectedChargeItems = ChargeItem::where('encounter_id', $this->encounter_id)
            ->with(['performerPractitioner'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function loadRapidAccess()
    {
        $this->rapidAccess = ServiceCatalog::active()->get();
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        // Buscar en el catálogo de servicios disponibles
        $this->results = ServiceCatalog::active()
            // ->where('client_id', $this->encounter->client_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->query.'%')
                    ->orWhere('description', 'like', '%'.$this->query.'%')
                    ->orWhere('cpt_code', 'like', '%'.$this->query.'%');
            })
            ->select('id', 'name', 'description', 'base_price', 'cpt_code', 'service_type', 'duration_minutes')
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->base_price,
                    'cpt_code' => $service->cpt_code,
                    'service_type' => $service->service_type,
                    'duration_minutes' => $service->duration_minutes,
                ];
            })
            ->toArray();
    }

    public function selectOption($serviceData)
    {
        $this->selectedServiceId = $serviceData['id'];
        $this->addServiceToEncounter();
        $this->results = [];
    }

    public function addServiceToEncounter()
    {
        $this->validate();

        $key = 'catalog-search';

        if (! $this->selectedServiceId) {
            $this->dispatch('error-'.$key,'Debe seleccionar un servicio válido.');
            return;
        }

        $service = ServiceCatalog::findOrFail($this->selectedServiceId);

        if (! $service) {

            $this->dispatch('error-'.$key,'Servicio no encontrado.');
            return;
        }

        try {
            DB::beginTransaction();

            $chargeItem = ChargeItem::whereEncounterId($this->encounter->id)->whereServiceCatalogId($this->selectedServiceId)->first();

            if (! $chargeItem) {
                // Crear ChargeItem usando el método del ServiceCatalog
                $chargeItem = $service->createChargeItem(
                    patientId: $this->encounter->patient_id,
                    practitionerId: $this->encounter->practitioner_id,
                    encounterId: $this->encounter->id,
                    appointmentId: $this->encounter->appointment_id,
                    quantity: $this->customQuantity,
                    customPrice: $this->customPrice
                );

                // Agregar notas si existen
                if ($this->serviceNotes) {
                    $chargeItem->update(['note' => $this->serviceNotes]);
                }

                DB::commit();

                // session()->flash('message.success', 'Servicio agregado exitosamente a la consulta.');

                $this->resetForm();
                $this->loadSelectedServices();

                $this->dispatch('saved-'.$key);

                // Dispatch event to fix scroll freeze after offcanvas closes
                $this->dispatch('service-selected');
            } else {
                $this->dispatch('error-'.$key,'¡Servicio ya esta agregado a la  consulta.!');
                // Also dispatch event to close offcanvas even on error
                $this->dispatch('service-selected');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error-'.$key,'Error al agregar el servicio: '.$e->getMessage());
        }
    }

    public function removeChargeItem($chargeItemId)
    {
        $chargeItem = ChargeItem::find($chargeItemId);

        if (! $chargeItem || $chargeItem->encounter_id != $this->encounter_id) {
            //session()->flash('error', 'ChargeItem no encontrado.');
            $this->dispatch('showToastrConsultation',
                type: 'success',
                message: 'Item no encontrado.'
            );
            return;
        }

        // Verificar que no esté facturado
        if ($chargeItem->status === 'billed') {
            //session()->flash('error', 'No se puede eliminar un servicio que ya ha sido facturado.');
            $this->dispatch('showToastrConsultation',
                type: 'success',
                message:'No se puede eliminar un servicio que ya ha sido facturado.'
            );
            return;
        }

        try {
            $chargeItem->delete();
            //session()->flash('message.success', 'Servicio eliminado del encounter.');
            $this->dispatch('showToastrConsultation',
                type: 'success',
                message: 'Servicio eliminado del encounter.'
            );
            $this->loadSelectedServices();
        } catch (\Exception $e) {
            //session()->flash('error', 'Error al eliminar el servicio.');
            $this->dispatch('showToastrConsultation',
                type: 'success',
                message: 'Error al eliminar el servicio.'
            );
        }
    }

    public function updateChargeItemQuantity($chargeItemId, $newQuantity)
    {
        if ($newQuantity <= 0) {
            return;
        }

        $chargeItem = ChargeItem::find($chargeItemId);

        if (! $chargeItem || $chargeItem->encounter_id != $this->encounter_id) {
            return;
        }

        if ($chargeItem->status === 'billed') {
            session()->flash('error', 'No se puede modificar un servicio que ya ha sido facturado.');

            return;
        }

        try {
            $chargeItem->update(['quantity' => $newQuantity]);
            session()->flash('message.success', 'Cantidad actualizada.');
            $this->loadSelectedServices();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la cantidad.');
        }
    }

    public function updateChargeItemPrice($chargeItemId, $newPrice)
    {
        if ($newPrice < 0) {
            return;
        }

        $chargeItem = ChargeItem::find($chargeItemId);

        if (! $chargeItem || $chargeItem->encounter_id != $this->encounter_id) {
            return;
        }

        if ($chargeItem->status === 'billed') {
            session()->flash('error', 'No se puede modificar un servicio que ya ha sido facturado.');

            return;
        }

        try {
            $chargeItem->update(['unit_price_value' => $newPrice]);
            session()->flash('message.success', 'Precio actualizado.');
            $this->loadSelectedServices();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el precio.');
        }
    }

    public function resetForm()
    {
        $this->reset(['query', 'selectedServiceId', 'customQuantity', 'customPrice', 'serviceNotes']);
        $this->customQuantity = 1;
        $this->results = [];
    }

    public function getTotalEncounterCharges()
    {
        return $this->selectedChargeItems->sum('total_price');
    }

    public function render()
    {
        return view('livewire.consultation.services');
    }
}
