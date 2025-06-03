<div id="marker-id-2">
    @foreach($items as $vs)
        <div class="input-block local-forms">
            <x-input-label  :value="$vs->name.' ('.$vs->default_unit.') '" />
            <x-text-input class="block w-full"
                          wire:model.live="values.{{$vs->code}}"
                          wire:keyup.debounce.300ms="save('{{$vs->code}}')"
                          wire:change="save('{{$vs->code}}')"
                          :value="$values[$vs->code]"
                          type="number"/>
            {{--}}
            <div class="text-sm text-gray-500 mt-1 text-end">
                {{__('Valores Normales')}} : Min {{$vs->min_normal_value}} , Max {{$vs->max_normal_value}}
            </div>
            {{--}}
            <!-- Indicadores de estado -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <!-- Spinner mientras guarda usando wire:loading -->
                    <div wire:loading wire:target="save('{{$vs->code}}')" class="flex items-center text-blue-600">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: inline-block">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm font-medium" style="display: inline-block">Guardando...</span>
                    </div>
                    <!-- Mensaje de guardado -->
                    @if($saved[$vs->code])
                        <div class="flex items-center text-green-600" id="saved-message">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Guardado</span>
                        </div>
                    @endif
                </div>
                @if (session()->has('error'))
                    <div class="mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

    @endforeach
</div>
