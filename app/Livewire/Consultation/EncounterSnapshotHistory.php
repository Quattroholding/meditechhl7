<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Services\EncounterSnapshotService;
use Livewire\Component;

class EncounterSnapshotHistory extends Component
{
    public $encounterId;

    public $encounter;

    public $snapshots = [];

    public $showModal = false;

    public $selectedSnapshot = null;

    public $selectedSnapshotData = null;

    public $snapshotChanges = null;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounterId);
        $this->loadSnapshots();
    }

    public function loadSnapshots()
    {
        if ($this->encounter) {
            $snapshotService = app(EncounterSnapshotService::class);
            $this->snapshots = $snapshotService->getAllSnapshots($this->encounter);
        }
    }

    public function viewSnapshot($snapshotId)
    {
        $snapshot = $this->snapshots->firstWhere('id', $snapshotId);

        if ($snapshot) {
            $this->selectedSnapshot = $snapshot;
            $this->selectedSnapshotData = $snapshot->snapshot_data;

            // Get previous snapshot for comparison
            $snapshotService = app(EncounterSnapshotService::class);
            $previousSnapshot = $this->snapshots
                ->where('version', $snapshot->version - 1)
                ->first();

            $this->snapshotChanges = $snapshotService->compareSnapshots($previousSnapshot, $snapshot);

            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedSnapshot = null;
        $this->selectedSnapshotData = null;
        $this->snapshotChanges = null;
    }

    public function render()
    {
        return view('livewire.consultation.encounter-snapshot-history');
    }
}
