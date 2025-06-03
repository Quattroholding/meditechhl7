<div>
    @if(count($selectedLists)>0)
        <table style="width:100%" class="medicine-table">
            <tbody>
            <tr>
                <td>Especialista</td>
                <td></td>
            </tr>
            @foreach($selectedLists as $s)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>{{$s->speciality->name}}</td>
                <td>
                    <div class="input-block local-forms">
                        <x-input-label for="physical_address" :value="__('Nota de Referencia')" />
                        <x-textarea-input wire:keyup.debounce.1000ms="setReason($event.target.value,{{$s->id}})" class="block mt-1 w-full">{{$selectedReason[$s->code]}}</x-textarea-input>
                        @include('partials.input_saving',['function'=>'setReason','saved'=>$savedNota])
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
                            <select wire:change.debounce.500ms="setEspecialist($event.target.value,{{$s->id}})" class="form-control">
                                    <option value="" selected="">Seleccione un especialista</option>
                                    @foreach($especialistas[$s->code] as $especialista)
                                        <option value="{{$especialista['id']}}" @if($selectedEspecialist[$s->code] == $especialista['id']) selected @endif>{{$especialista['name']}}</option>
                                    @endforeach
                            </select>
                            @include('partials.input_saving',['function'=>'setEspecialist','saved'=>$savedEspecialist])
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
    <div class="selector-field selector-field-on">
    @include('partials.input_saving',['function'=>'selectOption','saved'=>$saved])
    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar especialidad." >
    @if(!empty($results))
        <div class="selector-items" style="z-index: 1000">
            @foreach($results as $result)
                <div  class="sel-list-item"  wire:click="selectOption({{ json_encode($result) }})">
                    {{ $result['name'] }}
                </div>
            @endforeach
        </div>
    @endif
    </div>

    <div style="height:200px;">&nbsp;</div>
</div>
