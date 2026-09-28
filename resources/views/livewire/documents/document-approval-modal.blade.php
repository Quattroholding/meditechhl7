<div>
    @if ($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 1000px; width: 100%; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h2 class="modal-title">
                        {{ $document?->original_filename }}

                    </h2><br/>
                    <small>Tipo: {{ $document?->document_type->label() }}</small>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>

                </div>



            <!-- Content Grid: PDF (left) + Items (right) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- PDF Preview -->
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
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

                <!-- Items Table -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Datos Extraídos</h4>

                    @if ($validationMessages)
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs font-medium text-yellow-800 mb-2">Advertencias:</p>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                @foreach ($validationMessages as $message)
                                    <li>• {{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full">
                            <div
                                class="h-2 bg-blue-600 rounded-full"
                                style="width: {{ $confidenceScore * 100 }}%"
                            ></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ round($confidenceScore * 100) }}%</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 lg:grid-cols-1 gap-1 p-6">

                    <!-- Items Table -->
                    <div>
                        <div class="mb-3 flex gap-2">
                            <button wire:click="addItem" class="btn btn-success">+ Agregar Línea</button>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-2 text-left">
                                        <input type="checkbox" class="rounded" wire:model="selectedItems" />
                                    </th>
                                    <th class="px-3 py-2 text-left">SKU</th>
                                    <th class="px-3 py-2 text-left">Nombre</th>
                                    <th class="px-3 py-2 text-right">Cantidad</th>
                                    <th class="px-3 py-2 text-right">Costo Unit.</th>
                                    <th class="px-3 py-2 text-right">Desc. Unit.</th>
                                    <th class="px-3 py-2 text-right">Impuesto</th>
                                    <th class="px-3 py-2 text-right">Total</th>
                                    <th class="px-3 py-2 text-center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @forelse ($items as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedItems.{{ $index }}"
                                                class="rounded"
                                            />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                type="text"
                                                value="{{ $item['sku'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'sku', $event.target.value)"
                                                class="w-24 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                type="text"
                                                value="{{ $item['name'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'name', $event.target.value)"
                                                class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Nombre"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['quantity'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'quantity', $event.target.value)"
                                                class="w-16 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                min="0"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['unit_cost'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'unit_cost', $event.target.value)"
                                                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                step="0.01"
                                                min="0"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['discount'] ?? 0 }}"
                                                wire:change="updateItemField({{ $index }}, 'discount', $event.target.value)"
                                                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['tax'] ?? 0 }}"
                                                wire:change="updateItemField({{ $index }}, 'tax', $event.target.value)"
                                                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                step="0.01"
                                                min="0"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold">
                                            {{ number_format($itemTotals[$index] ?? 0, 2, '.', ',') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button
                                                wire:click="removeItem({{ $index }})"
                                                class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200"
                                                title="Eliminar"
                                            >
                                                ✕
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-3 py-4 text-center text-gray-500 text-sm">
                                            No hay items
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Summary -->
                        <div class="mt-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Subtotal</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($subtotal, 2, '.', ',') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Impuesto Total</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totalTax, 2, '.', ',') }}</p>
                                </div>
                                <div class="border-l border-gray-300 pl-4">
                                    <p class="text-gray-600">Total Factura</p>
                                    <p class="text-lg font-semibold text-blue-600">{{ number_format($totalInvoice, 2, '.', ',') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Notes -->
            <div class="border-t border-gray-200 px-6 py-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                <textarea
                    wire:model="notes"
                    placeholder="Añade notas sobre este documento..."
                    rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <!-- Rejection Form (if showing) -->
            @if ($isRejecting)
                <div class="border-t border-gray-200 px-6 py-4 bg-red-50">
                    <h4 class="font-semibold text-red-900 mb-3">Rechazar Documento</h4>
                    <textarea
                        wire:model="rejectionReason"
                        placeholder="Explica por qué rechazas este documento (mínimo 10 caracteres)..."
                        rows="3"
                        class="w-full px-3 py-2 border border-red-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    ></textarea>
                    @error('rejectionReason') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Actions -->

                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary">{{__('generic.cancel')}}</button>
                    @if ($isRejecting)
                        <button
                            type="button"
                            wire:click="cancelRejection"
                            class="btn btn-warning"
                        >
                            Volver
                        </button>
                        <button
                            type="button"
                            wire:click="confirmReject"
                            class="btn btn-danger"
                        >
                            Confirmar Rechazo
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="startRejecting"
                            class="btn btn-danger"
                        >
                            Rechazar
                        </button>
                        <button
                            type="button"
                            wire:click="approve"
                            class="btn btn-primary pull-right"
                        >
                            Aprobar
                        </button>
                    @endif
                </div>




            </div>
        </div>
    @endif
</div>
