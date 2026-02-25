<div class="">
    <!-- Services already added to encounter -->
    @if(count($selectedChargeItems) > 0)
        <div class="mb-4">
            <h6>{{ __('Servicios Agregados') }}</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Servicio') }}</th>
                            <th>{{ __('Cantidad') }}</th>
                            <th>{{ __('Precio Unit.') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedChargeItems as $chargeItem)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $chargeItem->service_description }}</div>
                                    @if($chargeItem->primary_cpt_code)
                                        <small class="text-muted">CPT: {{ $chargeItem->primary_cpt_code }}</small>
                                    @endif
                                    @if($chargeItem->note)
                                        <br><small class="text-info">{{ $chargeItem->note }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($chargeItem->status !== 'billed')
                                        <input type="number"
                                               value="{{ $chargeItem->quantity }}"
                                               wire:change="updateChargeItemQuantity({{ $chargeItem->id }}, $event.target.value)"
                                               class="form-control form-control-sm"
                                               style="width: 80px;"
                                               step="0.01"
                                               min="0.01">
                                    @else
                                        {{ $chargeItem->quantity }}
                                    @endif
                                </td>
                                <td>
                                    @if($chargeItem->status !== 'billed')
                                        <input type="number"
                                               value="{{ $chargeItem->unit_price_value }}"
                                               wire:change="updateChargeItemPrice({{ $chargeItem->id }}, $event.target.value)"
                                               class="form-control form-control-sm"
                                               style="width: 100px;"
                                               step="0.01"
                                               min="0">
                                    @else
                                        ${{ number_format($chargeItem->unit_price_value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($chargeItem->total_price, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $chargeItem->status->color() }}">
                                        {{ $chargeItem->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($chargeItem->status !== \App\Enums\ChargeItemStatus::BILLED)
                                        <button wire:click="removeChargeItem({{ $chargeItem->id }})"
                                                wire:confirm="¿Está seguro de eliminar este servicio?"
                                                class="btn btn-danger btn-sm"
                                                title="Eliminar servicio">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <!-- Add new service form -->
    <div class="">
        @php $id =\Illuminate\Support\Str::uuid();@endphp
        <div class="selector-field selector-field-on">
            <x-autosave-action save-key="catalog-search" />
            <table style="width:100%">
                <tbody>
                <tr>
                    <td style="width:80%;padding:20px;">
                        <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar servicio por nombre o codigo cpt" >
                    </td>
                    <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                        <div class="general-btn-small"
                             type="button"
                             data-bs-toggle="offcanvas"
                             data-bs-target="#offcanvasRight{{$id}}"
                             aria-controls="offcanvasRight">
                            <div class="general-btn-small-text general-btn-small-text-a">Listado de Servicios</div>
                            <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div wire:ignore.self class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" data-bs-backdrop="false" data-bs-scroll="true">
                <div class="offcanvas-body quick-items-content">
                    <div class="quick-items-close" data-bs-dismiss="offcanvas" aria-label="Cerrar" onclick="closeServicesOffcanvas('offcanvasRight{{$id}}')" style="cursor: pointer;">
                        <img src="/images/close-floating.png" alt="">
                    </div>
                    <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
                    <div id="rapid-access-services-{{$id}}">
                        @if(count($rapidAccess) > 0)
                            @foreach($rapidAccess as $i)
                                <div class="sel-list-item sel-code-{{$i->code}} mb-2 @if($i->is_selected) bg-primary text-white @endif"
                                     style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;"
                                     data-service-id="{{$i->id}}"
                                     onclick="selectServiceFromRapidAccess({{ json_encode($i) }}, '{{$id}}')">
                                    <div class="sel-list-item-code fw-bold">{{$i->cpt_code ?? $i->code }}</div>
                                    <div class="sel-list-item-content">{{$i->name}}</div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold">Precio a facturar: <span class="@if(!$i->is_selected) text-success @endif">${{ number_format($i['base_price'], 2) }}</span></div>
                                            @if(!empty($i['cpt_code']))
                                                <small class="@if(!$i->is_selected) text-muted @endif">CPT: {{ $i['cpt_code'] }}</small>
                                            @endif
                                            @if(!empty($i['description']) && $i['description'] !== $i['name'])
                                                <div class="@if(!$i->is_selected) text-muted @endif small">{{ Str::limit($i['description'], 100) }}</div>
                                            @endif
                                            <div class="@if(!$i->is_selected) text-info @endif small">
                                                Tipo: {{ ucfirst(str_replace('_', ' ', $i['service_type'])) }}
                                                @if($i['duration_minutes'])
                                                    | Duración: {{ $i['duration_minutes'] }} min
                                                @endif
                                            </div>
                                            @if($i->is_selected)
                                                <div class="mt-1 selected-indicator">
                                                    <small><i class="fas fa-check-circle"></i> Ya agregado</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <p>No hay servicios facturables configurados</p>
                            </div>
                        @endif
                    </div>
                    {{-- Botones de control del panel --}}
                    <div class="mt-4 d-flex gap-2 border-top pt-3">
                        <button type="button"
                                class="btn btn-sm btn-secondary"
                                onclick="closeServicesOffcanvas('offcanvasRight{{$id}}')">
                            <i class="fas fa-times"></i> Cerrar Panel
                        </button>
                    </div>
                </div>
            </div> <!-- end offcanvas-body-->

            {{-- RESULTADOS DE BÚSQUEDA --}}
            @if(!empty($results))
                <div class="selector-items" style="z-index: 1000">
                    @foreach($results as $result)
                        <div
                            class="sel-list-item row"
                            wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                            x-on:click=" window.dispatchEvent( new CustomEvent('autosave-start', { detail: 'diagnostic-search' }))"
                        >

                                {{ $result['name'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }
    </style>
    <script>
        // Store the Livewire component ID for later use
        const servicesComponentId = '{{ $this->getId() }}';

        // Function to select service from rapid access without closing offcanvas
        window.selectServiceFromRapidAccess = function(serviceData, offcanvasId) {
            // Call Livewire method using the component ID
            const component = Livewire.find(servicesComponentId);
            if (component) {
                component.selectFromRapidAccess(serviceData);
            }

            // Update visual state immediately
            setTimeout(() => {
                const container = document.getElementById('rapid-access-services-' + offcanvasId);
                if (container) {
                    const item = container.querySelector('[data-service-id="' + serviceData.id + '"]');
                    if (item && !item.classList.contains('bg-primary')) {
                        item.classList.add('bg-primary', 'text-white');
                        // Add indicator if it doesn't exist
                        if (!item.querySelector('.selected-indicator')) {
                            const indicator = document.createElement('div');
                            indicator.className = 'mt-1 selected-indicator';
                            indicator.innerHTML = '<small><i class="fas fa-check-circle"></i> Ya agregado</small>';
                            const content = item.querySelector('.flex-grow-1');
                            if (content) {
                                content.appendChild(indicator);
                            }
                        }
                    }
                }
            }, 100);
        };

        // Function to close offcanvas and restore scroll
        window.closeServicesOffcanvas = function(offcanvasId) {
            const el = document.getElementById(offcanvasId);
            if (el) {
                const instance = bootstrap.Offcanvas.getInstance(el);
                if (instance) {
                    instance.hide();
                }
            }

            // Force restore scroll
            setTimeout(() => {
                document.body.classList.remove('modal-open', 'offcanvas-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());
            }, 150);
        };

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrService', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });

            // Handle service added from rapid access - keep offcanvas open
            Livewire.on('service-added-from-rapid-access', (data) => {
                // Keep scroll enabled while offcanvas is open
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.body.classList.remove('modal-open');
            });

            // Fix scroll freeze after offcanvas closes (for search results selection)
            Livewire.on('service-selected', () => {
                setTimeout(() => {
                    document.body.classList.remove('modal-open', 'offcanvas-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());
                }, 100);
            });
        });

        // Listen for offcanvas hide events to ensure scroll is restored
        document.addEventListener('hidden.bs.offcanvas', function (event) {
            setTimeout(() => {
                document.body.classList.remove('modal-open', 'offcanvas-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());
            }, 100);
        });
    </script>
</div>
