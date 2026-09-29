<div wire:poll.2s="checkApprovalStatus">
    <div class="grid grid-cols-12 gap-6 mb-6">
        <!-- PDF Preview -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vista Previa del Documento</h5>
                </div>
                <div class="card-body p-0">
                    @if ($pdfUrl)
                        <embed src="{{ $pdfUrl }}" type="application/pdf" class="w-full" style="height: 600px;" />
                    @else
                        <div class="h-96 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>Documento no disponible</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Document Info -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Documento</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Nombre</label>
                        <p class="font-semibold">{{ $document->original_filename }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Tipo</label>
                        <p class="font-semibold">
                            <span class="badge badge-{{ $document->document_type->value === 'inventory' ? 'primary' : 'secondary' }}">
                                {{ $document->document_type->label() }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Estado</label>
                        <p class="font-semibold">
                            <span class="badge badge-{{ match($document->status->value) {
                                'pending' => 'warning',
                                'parsed' => 'info',
                                'approved' => 'success',
                                'processed' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ $document->status->label() }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Subido el</label>
                        <p class="font-semibold">{{ $document->created_at }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Confianza de Parsing</label>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div
                                    class="h-2 rounded-full"
                                    style="width: {{ $confidenceScore * 100 }}%; background-color: {{ $confidenceScore >= 0.8 ? '#10b981' : ($confidenceScore >= 0.6 ? '#f59e0b' : '#ef4444') }};"
                                ></div>
                            </div>
                            <span class="text-sm font-medium">{{ round($confidenceScore * 100) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($validationMessages)
            <div class="card border-l-4 border-yellow-400">
                <div class="card-header bg-yellow-50">
                    <h5 class="card-title mb-0 text-yellow-900">⚠️ Advertencias</h5>
                </div>
                <div class="card-body text-sm text-yellow-700">
                    <ul class="space-y-1">
                        @foreach ($validationMessages as $message)
                            <li>• {{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="card-title mb-0">Artículos Extraídos</h5>
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
                            <th style="width: 10%;">SKU</th>
                            <th style="width: 20%;">Nombre</th>
                            <th style="width: 10%;" class="text-right">Cantidad</th>
                            <th style="width: 10%;" class="text-right">Costo Unit.</th>
                            <th style="width: 10%;" class="text-right">Desc.</th>
                            <th style="width: 10%;" class="text-right">Impuesto</th>
                            <th style="width: 10%;">Tipo Unidad</th>
                            <th style="width: 5%;" class="text-center">Factor</th>
                            <th class="text-center">Total</th>
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
                                    value="{{ $item['sku'] ?? '' }}"
                                    wire:change="updateItemField({{ $index }}, 'sku', $event.target.value)"
                                    class="form-control form-control-sm"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    value="{{ $item['name'] ?? '' }}"
                                    wire:change="updateItemField({{ $index }}, 'name', $event.target.value)"
                                    class="form-control form-control-sm"
                                    placeholder="Nombre"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    value="{{ $item['quantity'] ?? '' }}"
                                    wire:change="updateItemField({{ $index }}, 'quantity', $event.target.value)"
                                    class="form-control form-control-sm text-right"
                                    min="0"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    value="{{ $item['unit_cost'] ?? '' }}"
                                    wire:change="updateItemField({{ $index }}, 'unit_cost', $event.target.value)"
                                    class="form-control form-control-sm text-right"
                                    step="0.01"
                                    min="0"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    value="{{ $item['discount'] ?? 0 }}"
                                    wire:change="updateItemField({{ $index }}, 'discount', $event.target.value)"
                                    class="form-control form-control-sm text-right"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-right">
                                <input
                                    type="number"
                                    value="{{ $item['tax'] ?? 0 }}"
                                    wire:change="updateItemField({{ $index }}, 'tax', $event.target.value)"
                                    class="form-control form-control-sm text-right"
                                    step="0.01"
                                    min="0"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td>
                                <select
                                    wire:change="updateItemField({{ $index }}, 'unit_type', $event.target.value)"
                                    class="form-control form-control-sm"
                                    @disabled($isApproving)
                                >
                                    <option value="presentation" @selected(($item['unit_type'] ?? 'internal') === 'presentation')>
                                        Presentación
                                    </option>
                                    <option value="internal" @selected(($item['unit_type'] ?? 'internal') === 'internal')>
                                        Unidad Interna
                                    </option>
                                </select>
                            </td>
                            <td class="text-center">
                                <input
                                    type="number"
                                    value="{{ $item['internal_units_per_presentation'] ?? 1 }}"
                                    wire:change="updateItemField({{ $index }}, 'internal_units_per_presentation', $event.target.value)"
                                    class="form-control form-control-sm text-center"
                                    style="width: 60px;"
                                    step="0.01"
                                    min="0.01"
                                    placeholder="1"
                                    @disabled($isApproving)
                                />
                            </td>
                            <td class="text-center font-semibold">
                                {{ number_format($itemTotals[$index] ?? 0, 2, '.', ',') }}
                            </td>
                            <td class="text-center">
                                <button
                                    wire:click="removeItem({{ $index }})"
                                    class="btn btn-sm btn-danger"
                                    title="Eliminar"
                                    @disabled($isApproving)
                                >
                                    ✕
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-gray-500 py-4">
                                No hay items
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Totals -->
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

    <!-- Rejection Form -->
    @if ($isRejecting)
    <div class="card border-l-4 border-danger mb-6">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Rechazar Documento</h5>
        </div>
        <div class="card-body">
            <textarea
                wire:model="rejectionReason"
                placeholder="Explica por qué rechazas este documento (mínimo 10 caracteres)..."
                rows="4"
                class="form-control @error('rejectionReason') is-invalid @enderror"
            ></textarea>
            @error('rejectionReason')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('documents.index') }}" class="btn btn-secondary" @if($isApproving) onclick="return false;" @endif>
            <i class="feather icon-x"></i> Cancelar
        </a>

        @if ($isRejecting)
            <button wire:click="cancelRejection" class="btn btn-warning" @disabled($isApproving)>
                <i class="feather icon-arrow-left"></i> Volver
            </button>
            <button wire:click="confirmReject" class="btn btn-danger" @disabled($isApproving)>
                <i class="feather icon-trash-2"></i> Confirmar Rechazo
            </button>
        @else
            <button wire:click="startRejecting" class="btn btn-danger" @disabled($isApproving)>
                <i class="feather icon-x-circle"></i> Rechazar
            </button>
            <button wire:click="approve" class="btn btn-primary" @disabled($isApproving) wire:loading.attr="disabled">
                @if ($isApproving)
                    <span wire:loading.remove>
                        <i class="feather icon-check-circle"></i> Aprobar
                    </span>
                    <span wire:loading>
                        <i class="spinner-border spinner-border-sm me-2" role="status"></i> Procesando...
                    </span>
                @else
                    <i class="feather icon-check-circle"></i> Aprobar
                @endif
            </button>
        @endif
    </div>
    <p>&nbsp;</p>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                console.log('Toastr event received:', event);
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
