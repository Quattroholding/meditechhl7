<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 900px;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="save">
                        <!-- Datos del Cliente -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fa fa-building"></i> Datos del Cliente</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Razón Social <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('client_company_name') is-invalid @enderror"
                                                   wire:model="client_company_name">
                                            @error('client_company_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>RUC <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('client_ruc') is-invalid @enderror"
                                                   wire:model="client_ruc">
                                            @error('client_ruc') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" class="form-control @error('client_phone') is-invalid @enderror"
                                                   wire:model="client_phone">
                                            @error('client_phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control @error('client_email') is-invalid @enderror"
                                                   wire:model="client_email">
                                            @error('client_email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dirección</label>
                                            <input type="text" class="form-control @error('client_address') is-invalid @enderror"
                                                   wire:model="client_address">
                                            @error('client_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fa fa-calendar"></i> Fechas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Emisión <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                                   wire:model.live="issue_date">
                                            @error('issue_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha de Vencimiento <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('expiration_date') is-invalid @enderror"
                                                   wire:model="expiration_date">
                                            @error('expiration_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Servicios -->
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fa fa-list"></i> Servicios</h6>
                                <button type="button" class="btn btn-sm btn-success" wire:click="addItem">
                                    <i class="fa fa-plus"></i> Agregar Servicio
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 30%;">Tipo de Servicio <span class="text-danger">*</span></th>
                                                <th style="width: 12%;">Cantidad <span class="text-danger">*</span></th>
                                                <th style="width: 15%;">Precio Unit. <span class="text-danger">*</span></th>
                                                <th style="width: 13%;">Subtotal</th>
                                                <th style="width: 20%;">Notas</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                            <tr wire:key="item-{{ $index }}">
                                                <td>
                                                    <select class="form-control form-control-sm @error('items.'.$index.'.service_type_id') is-invalid @enderror"
                                                            wire:model.live="items.{{ $index }}.service_type_id">
                                                        <option value="">Seleccione...</option>
                                                        @foreach($availableServices as $service)
                                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('items.'.$index.'.service_type_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                           wire:model.live="items.{{ $index }}.quantity" min="1">
                                                    @error('items.'.$index.'.quantity')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control form-control-sm @error('items.'.$index.'.unit_price') is-invalid @enderror"
                                                           wire:model.live="items.{{ $index }}.unit_price" min="0">
                                                    @error('items.'.$index.'.unit_price')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm bg-light"
                                                           value="${{ number_format($item['subtotal'], 2) }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                           wire:model="items.{{ $index }}.notes" placeholder="Opcional">
                                                </td>
                                                <td class="text-center">
                                                    @if(count($items) > 1)
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            wire:click="removeItem({{ $index }})">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('items')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Totales y Notas -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label><i class="fa fa-sticky-note"></i> Notas Adicionales</label>
                                            <textarea class="form-control" wire:model="notes" rows="4"
                                                      placeholder="Notas adicionales para el cliente..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-sm">
                                            <tr>
                                                <td class="text-right"><strong>Subtotal:</strong></td>
                                                <td class="text-right" style="width: 120px;">${{ number_format($subtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right"><strong>ITBMS ({{ $itbms_rate }}%):</strong></td>
                                                <td class="text-right">${{ number_format($itbms, 2) }}</td>
                                            </tr>
                                            <tr class="bg-primary text-white">
                                                <td class="text-right"><strong>TOTAL:</strong></td>
                                                <td class="text-right"><strong>${{ number_format($total, 2) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>&nbsp;&nbsp;
                    <button type="button" class="btn btn-primary" wire:click="save">
                        <i class="fa fa-save"></i> {{ $buttonSaveTitle }}
                    </button>
                </div>
            </div>
        </div>

    @endif
</div>
