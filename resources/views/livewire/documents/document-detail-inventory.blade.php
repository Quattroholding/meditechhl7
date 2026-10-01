<!-- Inventory Document Detail Card -->
<div>
    <div class="card mb-6">
        <div class="card-header bg-success-light">
            <h5 class="card-title mb-0">
                <i class="feather icon-package"></i> Artículos Extraídos
            </h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button wire:click="addItem" class="btn btn-success" @disabled($isApproving)>
                    <i class="feather icon-plus"></i> Agregar Línea
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                    <tr>
                        <th class="w-1"><input type="checkbox" class="form-check-input" wire:model="selectedItems" /></th>
                        <th style="width: 8%;">SKU</th>
                        <th style="width: 16%;">Nombre</th>
                        <th style="width: 8%;" class="text-right">Cantidad</th>
                        <th style="width: 8%;" class="text-right">Costo Unit.</th>
                        <th style="width: 8%;" class="text-right">Precio Venta</th>
                        <th style="width: 8%;" class="text-right">Desc. Unit.</th>
                        <th style="width: 8%;" class="text-right">Impuesto</th>
                        <th style="width: 8%;">Tipo Unidad</th>
                        <th style="width: 5%;" class="text-center">Factor</th>
                        <th style="width: 8%;" class="text-center">Total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($items as $index => $item)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    wire:model="selectedItems.{{ $index }}"
                                    class="form-check-input"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    wire:model="items.{{ $index }}.sku"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    wire:model="items.{{ $index }}.name"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    min="1"
                                    class="form-control form-control-sm text-end"
                                    wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control form-control-sm text-end"
                                    wire:model.live.debounce.500ms="items.{{ $index }}.unit_cost"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control form-control-sm text-end"
                                    wire:model.live.debounce.500ms="items.{{ $index }}.base_price"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control form-control-sm text-end"
                                    placeholder="0.00"
                                    title="Descuento por unidad"
                                    wire:model.live.debounce.500ms="items.{{ $index }}.discount_amount"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control form-control-sm text-end"
                                    placeholder="0.00"
                                    wire:model.live.debounce.500ms="items.{{ $index }}.tax_amount"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td>
                                <select class="form-select form-select-sm" wire:model="items.{{ $index }}.unit_type" @disabled($isApproving)>
                                    <option value="internal">Interna</option>
                                    <option value="presentation">Presentación</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="1"
                                    class="form-control form-control-sm text-center"
                                    wire:model.blur="items.{{ $index }}.internal_units_per_presentation"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ number_format($itemTotals[$index] ?? 0, 2) }}</span>
                            </td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    wire:click="removeItem({{ $index }})"
                                    class="btn btn-sm btn-danger"
                                    @disabled($isApproving)
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                No hay artículos extraídos
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mb-6">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notas (Opcional)</h5>
                </div>
                <div class="card-body">
                    <textarea
                        wire:model="notes"
                        placeholder="Añade notas sobre este documento..."
                        rows="4"
                        class="form-control"
                        @disabled($isApproving)
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumen Financiero</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($subtotal, 2, '.', ',') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span>Impuesto Total:</span>
                        <strong>${{ number_format($totalTax, 2, '.', ',') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-lg font-semibold">Total Factura:</span>
                        <strong class="text-lg text-primary">${{ number_format($totalInvoice, 2, '.', ',') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
