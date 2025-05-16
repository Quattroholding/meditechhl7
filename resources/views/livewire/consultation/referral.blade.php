<div>
    @if(count($selectedLists)>0)
        <x-input-label  :value="__('Especialidad')" />

        <table style="width:100%" class="medicine-table">
            <tbody>
            <tr>
                <td>Especialista</td>
                <td></td>
            </tr>
            @foreach($selectedLists as $s)
            <tr class="consultation-tr-inputs">
                <td>{{$s->speciality->name}}</td>
                <td>
                    <div class="input-block local-forms">
                        <x-input-label for="physical_address" :value="__('Nota de Referencia')" />
                        <x-textarea-input wire:keyup="setReason($event.target.value,{{$s->id}})" class="block mt-1 w-full">{{$selectedReason[$s->code]}}</x-textarea-input>
                    </div>
                    {{--}}
                    <div style="color:#0659d7;font-size: 11px;position: relative"> Límite: <span id="char_count_6825f4383fe80">207</span> de 1000 </div>
                    <div id="field_6825f4383f330"></div>
                    <div style="width:100%;height:10px;"></div>
                    {{--}}

                </td>
                <td></td>
                <td>
                    <div class="multivalue-item-sustento-container">
                        <div class="input-block local-forms">
                            <x-input-label for="especialist" :value="__('Especialista')" />
                            <select wire:change="setEspecialist($event.target.value,{{$s->id}})" class="form-control">
                                    <option value="" selected="">Seleccione un especialista</option>
                                    @foreach($especialistas[$s->code] as $especialista)
                                        <option value="{{$especialista['id']}}" @if($selectedEspecialist[$s->code] == $especialista['id']) selected @endif>{{$especialista['name']}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="sprite-trash-container" ani="1" style="cursor:pointer" onclick="remove_multivalue_item_value()">
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
    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar..." >

    <!-- Spinner de Carga -->
    <div wire:loading class="absolute right-2 top-2">
        <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-blue-500"></div>
    </div>


    @if(!empty($results))
        <ul class="absolute bg-white border w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto" style="z-index: 1000">
            @foreach($results as $result)
                <li  class="p-2 hover:bg-gray-200 cursor-pointer text-sm"  wire:click="selectOption({{ json_encode($result) }})">
                    {{ $result['name'] }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
