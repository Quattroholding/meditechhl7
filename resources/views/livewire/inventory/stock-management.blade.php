<div>
    <div class="page-header">
        <h3 class="page-title">Gestión de Stock</h3>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="input-block local-forms">
                        <x-input-label for="selectedItemId" value="Seleccionar Item" required/>
                        <livewire:components.inventory-item-selector wire:model="selectedItemId" :selectedItemId="$selectedItemId" :required="true" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-block local-forms">
                        <x-input-label for="operation" value="Operación"/>
                        <select wire:model.live="operation" class="form-select" id="operation">
                            <option value="receive">Recibir Stock</option>
                            <option value="adjust">Ajustar Stock</option>
                            <option value="transfer">Transferir Stock</option>
                            <option value="dispose">Dar de Baja</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($selectedItem)
                <div class="alert alert-info">
                    <strong>{{ $selectedItem->name }}</strong> |
                    Unidad: {{ $selectedItem->unit_of_measure }} |
                    Stock Total: {{ $selectedItem->inventoryReports->sum('quantity_on_hand') }}
                </div>

                <form wire:submit.prevent="executeOperation">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="input-block local-forms">
                                <x-input-label for="locationType" value="Tipo de Ubicación"/>
                                <select wire:model.live="locationType" class="form-select" id="locationType">
                                    @if(!auth()->user()->hasRole('doctor'))
                                    <option value="branch">Sucursal</option>
                                    @endif
                                    <option value="practitioner">Practitioner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-block local-forms">
                                <x-input-label for="locationId" value="Ubicación"/>
                                <select wire:model="locationId" class="form-select" id="locationId">
                                    <option value="">-- Seleccione --</option>
                                    @if($locationType === 'branch')
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    @else
                                        @foreach($practitioners as $pract)
                                            <option value="{{ $pract->id }}">{{ $pract->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('locationId')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-block local-forms">
                                <x-input-label for="quantity" value="Cantidad"/>
                                <x-text-input wire:model="quantity" class="block mt-1 w-full" type="number" step="0.01" name="quantity" id="quantity"/>
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    @if($operation === 'receive')
                        <div class="row mt-3">
                            <div class="col-12 col-md-4">
                                <div class="input-block local-forms">
                                    <x-input-label for="unitCost" value="Costo Unitario"/>
                                    <x-text-input wire:model="unitCost" class="block mt-1 w-full" type="number" step="0.01" name="unitCost" id="unitCost"/>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="input-block local-forms">
                                    <x-input-label for="lotNumber" value="Número de Lote"/>
                                    <x-text-input wire:model="lotNumber" class="block mt-1 w-full" type="text" name="lotNumber" id="lotNumber"/>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="input-block local-forms">
                                    <x-input-label for="expirationDate" value="Fecha de Vencimiento"/>
                                    <x-text-input wire:model="expirationDate" class="block mt-1 w-full" type="date" name="expirationDate" id="expirationDate"/>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($operation === 'transfer')
                        <div class="row mt-3">
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="transferToLocationType" value="Tipo Destino"/>
                                    <select wire:model.live="transferToLocationType" class="form-select" id="transferToLocationType">
                                        <option value="branch">Sucursal</option>
                                        <option value="practitioner">Practitioner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="transferToLocationId" value="Ubicación Destino"/>
                                    <select wire:model="transferToLocationId" class="form-select" id="transferToLocationId">
                                        <option value="">-- Seleccione --</option>
                                        @if($transferToLocationType === 'branch')
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($practitioners as $pract)
                                                <option value="{{ $pract->id }}">{{ $pract->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <x-input-error :messages="$errors->get('transferToLocationId')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(in_array($operation, ['adjust', 'dispose']))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="reason" value="Motivo"/>
                                    <textarea wire:model="reason" class="form-control" id="reason" rows="2"></textarea>
                                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Ejecutar Operación</button>
                    </div>
                </form>

                <hr class="my-4">
                <h5>Stock por Ubicación</h5>
                <table class="table table-sm">
                    <thead>
                        <tr><th>Ubicación</th><th>Stock</th><th>Disponible</th></tr>
                    </thead>
                    <tbody>
                        @foreach($selectedItem->inventoryReports as $report)
                            <tr>
                                <td>{{ $report->branch?->name ?? $report->practitioner?->name }}</td>
                                <td>{{ $report->quantity_on_hand }}</td>
                                <td>{{ $report->quantity_available }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
