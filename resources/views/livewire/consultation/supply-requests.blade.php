<div x-data="{}">
    {{-- Flash messages --}}
    @if(session()->has('supply-success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('supply-success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session()->has('supply-error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('supply-error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Lista de suministros seleccionados --}}
    @if(count($selectedSupplies) > 0)
        <table style="width:100%;" border="1" class="medicine-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Suministro</th>
                    <th style="width: 50%;">Detalles</th>
                    <th style="width: 15%;">Acción</th>
                </tr>
            </thead>
            <tbody>
            @foreach($selectedSupplies as $supply)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>
                    <b>{{ $supply->inventoryItem->name }}</b>
                    <div style="font-size: 0.85rem; color: #6c757d;">
                        SKU: {{ $supply->inventoryItem->sku }}
                    </div>
                    <div style="font-size: 0.85rem; color: #6c757d;">
                        Precio base: ${{ number_format($supply->inventoryItem->base_price, 2) }}
                    </div>
                </td>
                <td>
                    <table style="width:100%;">
                        <tbody>
                        <tr>
                            <td style="width: 50%;">
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity_{{ $supply->id }}" value="Cantidad" />
                                    <x-text-input
                                        type="number"
                                        wire:model.blur="quantities.{{ $supply->id }}"
                                        class="block mt-1 w-full"
                                        min="1"
                                        step="0.01"
                                        placeholder="Cantidad"
                                        id="quantity_{{ $supply->id }}"
                                    />
                                    <small class="text-muted">Unidad: {{ $supply->inventoryItem->unit_of_measure }}</small>
                                </div>
                            </td>
                            <td style="width: 50%;">
                                <div class="input-block local-forms">
                                    <x-input-label for="customPrice_{{ $supply->id }}" value="Precio personalizado (opcional)" />
                                    <x-text-input
                                        type="number"
                                        wire:model.blur="customPrices.{{ $supply->id }}"
                                        class="block mt-1 w-full"
                                        min="0"
                                        step="0.01"
                                        placeholder="Precio personalizado"
                                        id="customPrice_{{ $supply->id }}"
                                    />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="form-check" style="padding-left: 1.5rem;">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="isFree{{ $supply->id }}"
                                        wire:click="toggleFree({{ $supply->id }})"
                                        {{ $isFree[$supply->id] ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="isFree{{ $supply->id }}">
                                        <i class="fas fa-gift text-success"></i>
                                        Marcar como regalo (no cobrar al paciente)
                                    </label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="sprite-trash-container" style="cursor:pointer" wire:click="delete({{ $supply->id }})">
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

    {{-- Campo de búsqueda --}}
    <div class="selector-field selector-field-on">
        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:100%;padding:20px;">
                    <x-text-input
                        type="text"
                        wire:model.live="query"
                        class="block w-full"
                        placeholder="Buscar suministro por nombre, SKU o código de barras"
                    />
                </td>
            </tr>
            </tbody>
        </table>

        {{-- Resultados de búsqueda --}}
        @if(!empty($results))
            <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                {{-- Header --}}
                <div style="background: #ffffff; padding: 8px 12px; border-bottom: 2px solid #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.9rem;">
                            <i class="fas fa-boxes text-primary"></i>
                            <strong>Suministros disponibles</strong>
                        </div>
                        <div class="text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-list"></i>
                            {{ count($results) }} / {{ $totalResults }}
                            @if($hasMoreResults)
                                <span class="badge bg-primary ms-1">+{{ $totalResults - count($results) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contenedor con scroll --}}
                <div style="max-height: 320px; min-height: 200px; overflow-y: auto;">
                    @foreach($results as $result)
                        <div
                            class="sel-list-item"
                            wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                            style="padding: 10px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s;"
                            onmouseover="this.style.background='#f0f7ff'"
                            onmouseout="this.style.background='white'"
                        >
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex: 1;">
                                    <div style="font-size: 0.9rem; color: #212529; font-weight: 500;">
                                        {{ $result['name'] }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6c757d;">
                                        SKU: {{ $result['sku'] }} | Unidad: {{ $result['unit_of_measure'] }}
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6c757d;">
                                        Precio: ${{ number_format($result['base_price'], 2) }} {{ $result['currency'] }}
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    @if($result['stock'] > 0)
                                        <span class="badge bg-success" style="font-size: 0.85rem;">
                                            <i class="fas fa-check-circle"></i>
                                            Stock: {{ $result['stock'] }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.85rem;">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Sin stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Botón "Ver más" --}}
                    @if($hasMoreResults)
                        <div
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 99%; padding: 10px; text-align: center; cursor: pointer; color: white; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);"
                            wire:click.prevent="loadMore"
                            onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)'"
                        >
                            <div wire:loading.remove wire:target="loadMore">
                                <i class="fas fa-chevron-down" style="font-size: 1.2rem;"></i>
                                <div style="font-size: 1rem; margin-top: 4px;">
                                    <strong>CARGAR MÁS RESULTADOS</strong>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 4px; opacity: 0.9;">
                                    Hay {{ $totalResults - count($results) }} resultados más disponibles
                                </div>
                            </div>
                            <div wire:loading wire:target="loadMore">
                                <div class="spinner-border text-white" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
