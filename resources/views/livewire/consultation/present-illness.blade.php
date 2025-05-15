<div id="marker-id-3">
    @foreach($items as $key=>$item)
        <div class="p-6 text-gray-900" style="min-height: 100px;" id="marker-id-3.{{$loop->index}}">
            <h4>{{__($item['title'])}}</h4>
            <div class="selector-btn-type">
                <div  class="selector-items">
                    @foreach($item['items'] as $i)
                        @php
                            $pic = str_replace(' ','',$i->value);
                            $pic = str_replace('(','',$pic);
                            $pic = str_replace(')','',$pic);
                            $pic = str_replace('/','',$pic);
                             $pic = str_replace('-','',$pic);
                        @endphp
                        <div wire:click="save('{{$key}}','{{$i->value}}')" class="sel-list-item @if(in_array($this->$key,[$i->value,$i->value_esp])) location-active @endif">
                            <div>
                                <img src="/items/{{$pic}}.png" style="width:60px">
                            </div>
                            <span>{{$i->value}} | {{$i->value_esp}}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
    <div class="p-3 text-gray-900" style="min-height: 100px;" id="marker-id-3.5">
        <div class="input-block local-forms">
            <x-input-label  value="{{__('Factores Agravantes')}}" />
            <x-text-input class="block w-full" wire:model.live="aggravating_factors" :value="$aggravating_factors" type="text"/>
        </div>
        <div class="input-block local-forms">
            <x-input-label  value="{{__('Factores Atenuantes')}}" />
            <x-text-input class="block w-full" wire:model.live="alleviating_factors" :value="$alleviating_factors" type="text"/>
        </div>
        <div class="input-block local-forms">
            <x-input-label  value="{{__('Sintomas Asociados')}}" />
            <x-text-input class="block w-full" wire:model.live="associated_symptoms" :value="$associated_symptoms" type="text"/>
        </div>
        <div class="input-block local-forms">
            <x-input-label  value="{{__('Descripcion')}}" />
            <x-textarea-input
                wire:model.live="description"
                class="mt-1 block w-full bottom-0" rows="2">{{$description}}</x-textarea-input>
        </div>
    </div>
</div>
