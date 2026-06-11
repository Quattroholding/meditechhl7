<?php

namespace App\Livewire\Consultation;

use App\Models\RapidAccess;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SearchCptDropdown extends Component
{
    public $query = '';

    public $results = [];

    public $path;

    public $type;

    public $consultation_field_id;

    public $consultation;

    public $is_patient = false;

    public $selected = [];

    public $client_id;

    public function mount()
    {
        if ($this->type == 'laboratory') {
            $this->consultation_field_id = 6;
        }
        if ($this->type == 'images') {
            $this->consultation_field_id = 7;
        }
        if ($this->type == 'procedure') {
            $this->consultation_field_id = 8;
        }
        $this->setSelectedOptions();
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        // Para desarrollo local, desactivar verificación SSL
        $http = config('app.env') === 'local' ? Http::withoutVerifying() : Http::timeout(30);

        $response = $http->get($this->path, [
            'dropdown' => true,
            'q' => $this->query,
        ]);

        // $this->results = $response->json() ?? []; LO CAMBIE PORQUE NO FUNCIONABAN LOS DROPDOWNS DE ACCESOS RAPIDOS 16/04/26
        $this->results = $response->json('data') ?? [];
    }

    public function selectOption($option)
    {

        RapidAccess::create([
            'type' => 'CLIENT',
            'client_id' => $this->client_id,
            'user_id' => auth()->user()->id,
            'encounter_section_id' => $this->consultation_field_id,
            'cpt_id' => $option['id'],
        ]);

        $this->selectedOption = $option;
        $this->query = $option['name']; // Asigna el nombre seleccionado al input
        $this->results = []; // Limpia los resultados

        $this->setSelectedOptions();
    }

    public function setSelectedOptions()
    {
        if (auth()->user()->clients()->first()) {
            $this->client_id = auth()->user()->clients()->first()->id;
            $this->selected = RapidAccess::whereClientId(auth()->user()->clients()->first()->id)
                ->whereHas('cpt', function ($q) {
                    $q->whereType($this->type);
                })->whereEncounterSectionId($this->consultation_field_id)->whereType('CLIENT')->get();

        }

        $this->dispatch('refreshSetupReminders');
    }

    public function delete($id)
    {
        $rapidAccess = RapidAccess::find($id);

        if ($rapidAccess) {
            $rapidAccess->delete();
            $this->dispatch('notify', ['message' => 'Acceso rápido eliminado correctamente', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'El registro ya no existe', 'type' => 'warning']);
        }

        $this->setSelectedOptions();
    }

    public function clearInput()
    {

        $this->selectedOption = '';
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.consultation.search-cpt-dropdown');
    }
}
