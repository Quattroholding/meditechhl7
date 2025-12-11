<?php

namespace App\Livewire\Consultation;

use Livewire\Component;
use Livewire\Attributes\On;

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
        $this->rapidAccess = \App\Models\RapidAccess::whereUserId(auth()->id())
            ->whereType('CLIENT')
            ->whereEncounterSectionId($this->section_id)
            ->get();

        if ($this->rapidAccess->count() == 0) {
            $this->rapidAccess = \App\Models\RapidAccess::whereType('MASTER')
                ->whereEncounterSectionId($this->section_id)
                ->get();
        }
    }

    public function selectItem($cptId)
    {
        // Disparar evento ÚNICO al componente padre usando el ID del encuentro y sección
        $this->dispatch('rapid-access-item-selected-' . $this->encounterId . '-' . $this->section_id, cptId: $cptId);
    }


    public function render()
    {
        return view('livewire.consultation.rapid-access-offcanvas');
    }
}
