<div>
    @if(count($selectedLists)>0)
        <table style="width:100%;" border="1"
               class="medicine-table">
            <tbody>
            <tr>

            </tr>
            @foreach($selectedLists as $m)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>
                    <b rel="producto-full-name">{{$m->medicine->full_name}}</b>
                </td>
                <td>
                    <table style="width:100%;">
                        <tbody>
                        <tr>
                            <td >
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity" :value="__('Cantidad')" />
                                    <x-text-input  wire:keyup="updateField({{$m->id}},$event.target.value,'quantity')"
                                                   wire:model="quantitys.{{$m->id}}"
                                                   name="quantity" type="number"
                                                   class="block mt-1 w-full" placeholder="Ejemplo : 2 tabletas"/>
                                </div>
                                <div class="input-block local-forms">
                                    <x-input-label for="frecuency" :value="__('Frecuencia (Horas)')" />
                                    <x-text-input  wire:keyup="updateField({{$m->id}},$event.target.value,'frequency')"
                                                   wire:model="frecuencies.{{$m->id}}"
                                                   name="frequency" type="number"
                                                   class="block mt-1 w-full" placeholder="Ejemplo : Cada 12 horas"/>
                                </div>

                            </td>
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="route" :value="__('Via')" />
                                    @isset($routes[$m->id])
                                        <x-select-input wire:change="updateField({{$m->id}},$event.target.value,'route')"
                                                        wire:model="routes.{{$m->id}}"
                                                        name="route" :options="\App\Models\Lista::medicationVias()" :selected="[$routes[$m->id]]"
                                                        class="block mt-1 w-full"/>
                                    @else
                                        <x-select-input wire:change="updateField({{$m->id}},$event.target.value,'route')"
                                                        wire:model="routes.{{$m->id}}"
                                                        name="route" :options="\App\Models\Lista::medicationVias()" :selected="[]"
                                                        class="block mt-1 w-full"/>
                                    @endif

                                </div>

                                <div class="input-block local-forms">
                                    <x-input-label for="duration" :value="__('Duración (Días)')" />
                                    <x-text-input  wire:keyup="updateField({{$m->id}},$event.target.value,'duration')"
                                                   wire:model="durations.{{$m->id}}"
                                                   name="duration" type="number"  class="block mt-1 w-full" placeholder="Ejemplo : por 5 dias"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text" :value="__('Indicciones')"/>
                                    <textarea wire:keyup="updateField({{$m->id}},$event.target.value,'dosage_text')"
                                              wire:model="dosage_texts.{{$m->id}}"
                                              maxlength="500" class="char-lenght-count-control form-control field-medicine-plan textarea-full-bg"
                                              placeholder="Ejemplo: Una tableta cada 8 horas vía oral por 5 días">
                                        @isset($dosage_texts[$m->id])
                                            {{$dosage_texts[$m->id]}}
                                        @endisset
                                    </textarea>
                                </div>
                            </td>
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="refills" :value="__('Meses de Refill')" />
                                    <select class="form-control" wire:change="updateField({{$m->id}},$event.target.value,'refills')">
                                        <option value="">Sin Refill</option>
                                        @for($i=2;$i<6;$i++)
                                            <option value="{{$i}}" @if($m->refills==$i) selected @endif>{{$i}} meses</option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="sprite-trash-container" ani="1" style="cursor:pointer" wire:click="delete({{$m->id}})">
                        <div class="sprite-trash"></div>
                        <div>Borrar</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    @endif
    <div class="my-3"></div>
    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar medicamento." >

    @include('partials.input_saving',['function'=>'selectOption','saved'=>$saved])

    @if(!empty($results))
        <ul class="absolute bg-white border w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto" style="z-index: 1000">
            @foreach($results as $result)
                <li  class="p-2 hover:bg-gray-200 cursor-pointer text-sm"  wire:click="selectOption({{ json_encode($result) }})">
                    {{ $result['name'] }}
                </li>
            @endforeach
        </ul>
    @endif

   <div style="height:200px;">&nbsp;</div>
</div>
