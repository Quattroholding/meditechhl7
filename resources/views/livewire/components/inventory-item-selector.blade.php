<div class="inventory-item-selector-wrapper"
     x-data="{
         hideTimeout: null,
         hideDropdown() {
             this.hideTimeout = setTimeout(() => {
                 $wire.showDropdown = false;
             }, 200);
         },
         cancelHide() {
             if (this.hideTimeout) {
                 clearTimeout(this.hideTimeout);
                 this.hideTimeout = null;
             }
         }
     }">

    <!-- Hidden input for form submission -->
    <input type="hidden" wire:model="selectedItemId" name="inventory_item_id">

    <!-- Search input -->
    <div class="position-relative">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="searchTerm"
                @focus="$wire.showDropdown = true"
                @blur="hideDropdown()"
                class="form-control {{ $required && !$selectedItemId ? 'is-invalid' : '' }}"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
            >

            @if($selectedItemId && $selectedItemName)
                <button
                    type="button"
                    wire:click="clearSelection"
                    class="btn btn-outline-secondary btn-sm"
                    title="Limpiar selección"
                >
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <!-- Dropdown results -->
        @if($showDropdown && count($items) > 0)
            <div
                class="item-dropdown position-absolute w-100 bg-white border border-top-0 shadow-sm"
                style="z-index: 1050; max-height: 400px; overflow-y: auto;"
                @mouseenter="cancelHide()"
                @mouseleave="hideDropdown()"
            >
                @foreach($items as $item)
                    <div
                        wire:click="selectItem({{ $item->id }})"
                        class="item-option p-3 border-bottom cursor-pointer hover:bg-gray-50"
                        style="cursor: pointer;"
                        @mousedown.prevent
                    >
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">
                                    <i class="fas fa-box-open text-primary me-2"></i>
                                    {{ $item->name }}
                                </div>
                                <div class="text-muted small mt-1">
                                    <span class="badge bg-info me-2">{{ $item->item_type->label() }}</span>
                                    SKU: <strong>{{ $item->sku }}</strong>
                                    @if($item->barcode)
                                        | Código: {{ $item->barcode }}
                                    @endif
                                </div>
                                @if($item->description)
                                    <div class="text-muted small mt-1">
                                        {{ Str::limit($item->description, 80) }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-end ms-3">
                                <div class="text-primary fw-bold">
                                    ${{ number_format($item->base_price, 2) }}
                                </div>
                                <div class="text-muted small">
                                    {{ $item->unit_of_measure->label() }}
                                </div>
                                @php
                                    $totalStock = $item->inventoryReports->sum('quantity_on_hand');
                                @endphp
                                @if($totalStock > 0)
                                    <div class="badge bg-success mt-1">
                                        <i class="fas fa-check-circle"></i> Stock: {{ $totalStock }}
                                    </div>
                                @else
                                    <div class="badge bg-danger mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Sin stock
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- No results message -->
        @if($showDropdown && strlen($searchTerm) >= 2 && count($items) === 0)
            <div
                class="position-absolute w-100 bg-white border border-top-0 p-3 text-center text-muted"
                style="z-index: 1050;"
                @mouseenter="cancelHide()"
            >
                <i class="fas fa-search me-2"></i>
                No se encontraron items con "{{ $searchTerm }}"
            </div>
        @endif

        <!-- Loading indicator -->
        <div wire:loading wire:target="searchTerm" class="position-absolute end-0 top-50 translate-middle-y me-5">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
        </div>
    </div>

    <style>
        .inventory-item-selector-wrapper {
            position: relative;
        }

        .item-dropdown {
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
        }

        .item-option:hover {
            background-color: #f8f9fa !important;
        }

        .item-option:last-child {
            border-bottom: none;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</div>
