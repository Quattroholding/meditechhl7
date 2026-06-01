<div id="marker-id-3">
    @if($mode === 'full')
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
                            <div @if($key=='location' && is_array($this->$key['location']) && (in_array($i->value,$this->$key['location']) or in_array($i->value_esp,$this->$key['location'])))
                                    class="sel-list-item   location-active"
                                    wire:click="delete('{{$key}}','{{$i->value}}')"
                                @elseif(in_array($this->$key,[$i->value,$i->value_esp]))
                                     class="sel-list-item   location-active"
                                @else
                                     class="sel-list-item"
                                     wire:click="save('{{$key}}','{{$i->value}}')"
                                @endif>
                                <div>
                                    <img src="/items/{{$pic}}.png" style="width:60px">
                                </div>
                                <span> {{ucfirst($i->value_esp)}} | {{ucfirst($i->value)}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="p-3 text-gray-900" style="min-height: 100px;" id="marker-id-3.5">
        @if($mode === 'full')
            <div class="input-block local-forms">
                <x-input-label  value="{{ __('consultation.present_illness_section.aggravating_factors') }}" />
                <x-autosave-input
                    type="text"
                    :value="$aggravating_factors"
                    class="form-control mt-1 block w-full"
                    wire:model.live.debounce.500ms="aggravating_factors"
                    save-method="saveAggravatingFactors"
                    save-key="aggravating_factors"
                />
            </div>
            <div class="input-block local-forms">
                <x-input-label  value="{{ __('consultation.present_illness_section.alleviating_factors') }}" />
                <x-autosave-input
                    type="text"
                    :value="$alleviating_factors"
                    class="form-control mt-1 block w-full"
                    wire:model.live.debounce.500ms="alleviating_factors"
                    save-method="saveAlleviatingFactors"
                    save-key="alleviating_factors"
                />
            </div>
            <div class="input-block local-forms">
                <x-input-label  value="{{ __('consultation.present_illness_section.associated_symptoms') }}" />
                <x-autosave-input
                    type="text"
                    :value="$associated_symptoms"
                    class="form-control mt-1 block w-full"
                    wire:model.live.debounce.500ms="associated_symptoms"
                    save-method="saveAssociatedSymptoms"
                    save-key="associated_symptoms"
                />
            </div>
        @endif

        <div class="input-block local-forms">
            <x-input-label  value="{{ __('consultation.present_illness_section.description') }}" />
            <x-autosave-input
                type="textarea"
                :value="$description"
                class="form-control mt-1 block w-full"
                wire:model.live.debounce.500ms="description"
                save-method="saveDescription"
                save-key="description"
            />
        </div>
    </div>
</div>
