<div>
    @if(count($selectedLists)>0)
        <x-input-label  :value="__('Medicinas')" />
        <table style="width:100%"
               class="medicine-table">
            <tbody>
            <tr>

            </tr>
            @foreach($selectedLists as $m)
            <tr class="consultation-tr-inputs"><td>
                    <b rel="producto-full-name">{{$m->medicine->full_name}}</b>
                </td>
                <td>
                    <table style="width:100%">
                        <tbody>
                        <tr>
                            <td colspan="3">
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text" :value="__('Indicciones')" />
                                    <textarea wire:keyup="updateField({{$m->id}},$event.target.value,'dosage_text')" maxlength="500" class="char-lenght-count-control form-control field-medicine-plan textarea-full-bg"
                                              placeholder="Ejemplo: Una tableta cada 8 horas vía oral por 5 días">
                                        {{$m->dosage_text}}
                                    </textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity" :value="__('Cantidad')" />
                                    <input wire:keyup="updateField({{$m->id}},$event.target.value,'quantity')" type="number" class="form-control"
                                       placeholder="Ejemplo: 15"
                                       value="{{$m->quantity}}">
                                </div>
                            </td>
                            <td>
                                <div id="selector-6825f4389ec85" class="prerendered-selector">
                                    <div class="input-block local-forms">
                                        <x-input-label for="refills" :value="__('Meses de Refill')" />
                                        <select class="form-control" wire:change="updateField({{$m->id}},$event.target.value,'refills')">
                                            <option value="">Sin Refill</option>
                                            @for($i=2;$i<6;$i++)
                                            <option value="{{$i}}" @if($m->refills==$i) selected @endif>{{$i}} meses</option>
                                            @endfor
                                        </select>
                                    </div>
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
