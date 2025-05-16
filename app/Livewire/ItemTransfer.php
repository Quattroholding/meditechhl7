<?php

namespace App\Livewire;

use App\Models\ConsultationField;
use App\Models\ConsultationFieldTemplate;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use Livewire\Component;

class ItemTransfer extends Component
{
    public $availableItems = [];
    public $selectedItems = [];

    public $client_id;

    public $category=null;

    public function mount()
    {
        // Por ejemplo, llenar con datos de una base
        $disponibles = EncounterSection::pluck('id');
        $selecionados =[];

        if(auth()->user()->clients()->first()){
            $this->client_id = auth()->user()->clients()->first()->id;
            $selecionados = EncounterTemplate::whereClientId($this->client_id)->whereType('client')->pluck('encounter_section_id');
        }

        if(count($selecionados)==0){
            $this->availableItems =EncounterSection::whereCategory($this->category)->get()->toArray();
            $this->selectedItems = [];
        }else{

            $diff = $disponibles->diff($selecionados);
            $diff2 = $selecionados->diff($disponibles);

            $this->availableItems = EncounterSection::whereCategory($this->category)->whereIn('id',$diff)->get()->toArray();
            $this->selectedItems = EncounterSection::whereCategory($this->category)->whereIn('id',$selecionados)->get()->toArray();;
        }

        //dd($this->availableItems);
    }

    public function moveToSelected($itemId)
    {
        $item = collect($this->availableItems)->firstWhere('id', $itemId);
        if ($item) {
            $this->availableItems = array_values(array_filter($this->availableItems, fn($i) => $i['id'] !== $itemId));
            $this->selectedItems[] = $item;
        }

        EncounterTemplate::create([
            'type'=>'client',
            'client_id'=>$this->client_id,
            'user_id'=>auth()->user()->id,
            'encounter_section_id'=>$itemId,
        ]);
    }

    public function moveToAvailable($itemId)
    {
        $item = collect($this->selectedItems)->firstWhere('id', $itemId);
        if ($item) {
            $this->selectedItems = array_values(array_filter($this->selectedItems, fn($i) => $i['id'] !== $itemId));
            $this->availableItems[] = $item;
        }

        $field = EncounterTemplate::whereRaw("(client_id = ".$this->client_id." or user_id = ".auth()->user()->id.")")->whereEncounterSectionId($itemId)->first();
        $field->delete();
    }

    public function updateOrder($orderedIds)
    {
        $this->selectedItems = collect($orderedIds)->map(function ($id) {
            return collect($this->selectedItems)->firstWhere('id', $id);
        })->toArray();
    }

    public function render()
    {
        return view('livewire.item-transfer');
    }
}
