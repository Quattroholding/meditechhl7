<div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>  {{ __('Agregar Cpt') }}</h4>
                        </div>
                    </div>
                    <form wire:submit="saveCpt">
                    @csrf
                        <div class="col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar Procedimiento" >
                                @if(!empty($results))
                                    <ul class="absolute bg-white border w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto" style="z-index: 1000">
                                        @foreach($results as $result)
                                            <li class="p-2 hover:bg-gray-200 cursor-pointer text-sm" wire:click="selectOption({{ json_encode($result) }})" >
                                                {{ $result['name'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @error('cpt_id') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <!-- PRICE -->
                        <div class=" col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <x-input-label for="current_price" :value="__('Precio')" />
                                <x-text-input wire:model="current_price_cpt" class="block mt-1 w-full" type="number" step="any" name="current_price_cpt" :value="old('current_price_cpt')"/>
                                @error('current_price_cpt') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>  {{ __('Crear procediminto personalizado') }}</h4>
                        </div>
                    </div>
                    <form wire:submit="saveCustom">
                        @csrf
                        <!-- DESCRIPCION -->
                        <div class=" col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <x-input-label for="description" :value="__('Descripcion')" />
                                <x-text-input wire:model="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')"/>
                                @error('description') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <!-- BLOOD TYPE -->
                        <div class=" col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <x-input-label for="type" :value="__('Type')" class="local-top"/>
                                <x-select-input wire:model="type" name="type" :options="\App\Models\Lista::userProcedureTypes()" :selected="[null]" class="block w-full"/>
                                @error('type') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <!-- PRICE -->
                        <div class=" col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <x-input-label for="current_price" :value="__('Precio')" />
                                <x-text-input wire:model="current_price" class="block mt-1 w-full" type="number" step="any" name="current_price" :value="old('current_price')"/>
                                @error('current_price') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.register') }} </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body" style="overflow-x: scroll">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>  {{ __('Procedimientos creados') }}</h4>
                        </div>
                    </div>
                    <table  class="table border-0 custom-table comman-table mb-0" >
                        <thead>
                        <th>{{__('Código')}}</th>
                        <th>{{__('Descripción')}}</th>
                        <th>{{__('Precio')}}</th>
                        <th>{{__('Acción')}}</th>
                        </thead>
                        <tbody>
                        @foreach($created as $c)
                            <tr>
                                <td>{{$c->code}}</td>
                                <td>
                                    @empty(!$c->cpt_code)
                                        {{$c->cpt->description_es}}
                                    @else
                                        {{$c->description}}
                                    @endempty
                                </td>
                                <td>{{$c->current_price}}</td>
                                <td>
                                    <div wire:click="delete({{$c->id}})" title="{{__('Borrar')}}"><i class="fa fa-trash"></i> </div>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
