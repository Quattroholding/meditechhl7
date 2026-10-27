<div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>  {{ __('Agregar precio procedimiento') }}</h4>
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
                                <x-text-input wire:model="current_price_cpt" class="block mt-1 w-full" type="number" step="any" name="current_price_cpt"/>
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
                                <x-text-input wire:model="description" class="block mt-1 w-full" type="text" name="description"/>
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
                                <x-text-input wire:model="current_price" class="block mt-1 w-full" type="number" step="any" name="current_price"/>
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
                        <th>{{__('Cuenta Contable')}}</th>
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
                                    @if($c->accountingAccount)
                                        <small>{{ $c->accountingAccount->code }} - {{ $c->accountingAccount->name }}</small>
                                    @else
                                        <small class="text-muted">No asignada</small>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="openAccountingModal({{$c->id}})" class="btn btn-sm btn-info me-2" title="Asignar Cuenta Contable">
                                        <i class="fa fa-edit"></i> Asignar
                                    </button>
                                    <div wire:click="delete({{$c->id}})" title="{{__('Borrar')}}" style="display: inline-block; cursor: pointer;">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para Asignar Cuenta Contable -->
    @if($editingProcedureId)
        <div class="modal fade show" id="accountingModal" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Asignar Cuenta Contable</h5>
                        <button type="button" class="close" wire:click="closeAccountingModal()">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form wire:submit="saveAccounting">
                        <div class="modal-body">
                            <!-- Cuenta Contable -->
                            <div class="form-group mb-3">
                                <label for="accountingAccountId" class="form-label">Cuenta Contable *</label>
                                <select
                                    id="accountingAccountId"
                                    wire:model="accountingAccountId"
                                    class="form-select @error('accountingAccountId') is-invalid @enderror"
                                >
                                    <option value="">Seleccionar cuenta</option>
                                    @foreach($this->getAccountingAccountsProperty() as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('accountingAccountId')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Centro de Costo -->
                            <div class="form-group mb-3">
                                <label for="costCenterId" class="form-label">Centro de Costo</label>
                                <select
                                    id="costCenterId"
                                    wire:model="costCenterId"
                                    class="form-select @error('costCenterId') is-invalid @enderror"
                                >
                                    <option value="">Sin asignar</option>
                                    @foreach($this->getCostCentersProperty() as $center)
                                        <option value="{{ $center->id }}">
                                            {{ $center->code }} - {{ $center->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('costCenterId')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeAccountingModal()">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
