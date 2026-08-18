<div id="marker-id-3">
    @if($mode === 'full')
        @foreach($items as $key=>$item)
            <div class="p-6 text-gray-900" style="min-height: 100px;" id="marker-id-3.{{$loop->index}}">
                <h4 style="color: var(--primary-color); font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{__($item['title'])}}
                </h4>
                <div class="selector-btn-type">
                    <div  class="selector-items">
                        @foreach($item['items'] as $i)
                            @php
                                // Usar solo la versión en inglés para el nombre del archivo
                                $pic = strtolower($i->value);
                                $pic = str_replace(' ','',$pic);
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
                                <div style="margin-bottom: 8px;">
                                    <img src="/items/{{$pic}}.png"
                                         style="width:140px; height:110px; object-fit: contain;"
                                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         alt="{{$i->value}}">
                                    <div style="display: none; width: 140px; height: 110px; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 8px;">
                                        <i class="fas fa-clock" style="font-size: 55px; color: var(--primary-color);"></i>
                                    </div>
                                </div>
                                <span> {{ucfirst($i->value_esp)}} | {{ucfirst($i->value)}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="p-3 text-gray-900" style="min-height: 100px; margin-top: 30px;" id="marker-id-3.5">
        @if($mode === 'full')
            <h4 style="color: var(--primary-color); font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                <i class="fas fa-notes-medical me-2"></i>
                Información Adicional
            </h4>
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
