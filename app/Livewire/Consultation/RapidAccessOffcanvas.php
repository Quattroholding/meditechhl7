<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\RapidAccess;
use Livewire\Component;

class RapidAccessOffcanvas extends Component
{
    public $section_id;

    public $rapidAccess = [];

    public $offcanvasId;

    public $encounterId;

    public function mount($sectionId, $offcanvasId, $encounterId)
    {
        $this->section_id = $sectionId;
        $this->offcanvasId = $offcanvasId;
        $this->encounterId = $encounterId;
        $this->loadRapidAccess();
    }

    public function loadRapidAccess()
    {
        $encounter = Encounter::find($this->encounterId);

        // Determinar el tipo de servicio según la sección
        $serviceType = 'procedure';
        if ($this->section_id == 6) {
            $serviceType = 'laboratory';
        } elseif ($this->section_id == 7) {
            $serviceType = 'images';
        }

        // Obtener los códigos CPT ya seleccionados en esta consulta
        $selectedCodes = $encounter->serviceRequests()
            ->where('service_type', $serviceType)
            ->pluck('code')
            ->toArray();

        $this->rapidAccess = RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->get()
            ->map(function ($item) use ($selectedCodes) {
                $item->is_selected = in_array($item->cpt->code, $selectedCodes);

                return $item;
            });

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->get()
                ->map(function ($item) use ($selectedCodes) {
                    $item->is_selected = in_array($item->cpt->code, $selectedCodes);

                    return $item;
                });
        }
    }

    public function selectItem($cptId)
    {
        // Disparar evento ÚNICO al componente padre usando el ID del encuentro y sección
        $this->dispatch('rapid-access-item-selected-'.$this->encounterId.'-'.$this->section_id, cptId: $cptId);

        // Recargar la lista para actualizar el indicador visual
        $this->loadRapidAccess();
    }

    public function getListeners()
    {
        return [
            'rapid-access-list-updated-'.$this->encounterId.'-'.$this->section_id => 'loadRapidAccess',
        ];
    }

    public function render()
    {
        return view('livewire.consultation.rapid-access-offcanvas');
    }
}
