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
                                    <span class="badge bg-{{ $chargeItem->status === 'billable' ? 'success' : ($chargeItem->status === 'billed' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($chargeItem->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($chargeItem->status !== 'billed')
                                        <button wire:click="removeChargeItem({{ $chargeItem->id }})"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Está seguro de eliminar este servicio?')"
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
            <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel">


                <div class="offcanvas-body  quick-items-content">
                    <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                        <img src="/images/close-floating.png" alt="">
                    </div>
                    <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
                    @if(count($rapidAccess) > 0)
                        @foreach($rapidAccess as $i)
                            <div class="sel-list-item sel-code-{{$i->code}} mb-2" style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;">

                                {{-- Contenido principal clickeable --}}
                                <div wire:click="selectOption({{ json_encode($i) }})">
                                    <div class="sel-list-item-code fw-bold">{{$i->cpt_code ?? $i->code }}</div>
                                    <div class="sel-list-item-content">{{$i->name}}</div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold">  Precio a facturar :<div class="text-success"> ${{ number_format($i['base_price'], 2) }}</div></div>
                                            @if(!empty($i['cpt_code']))
                                                <small class="text-muted">CPT: {{ $i['cpt_code'] }}</small>
                                            @endif
                                            @if(!empty($i['description']) && $i['description'] !== $i['name'])
                                                <div class="text-muted small">{{ Str::limit($i['description'], 100) }}</div>
                                            @endif
                                            <div class="text-info small">
                                                Tipo: {{ ucfirst(str_replace('_', ' ', $i['service_type'])) }}
                                                @if($i['duration_minutes'])
                                                    | Duración: {{ $i['duration_minutes'] }} min
                                                @endif
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <p>No hay sericios facturables configurados</p>
                        </div>
                    @endif
                    {{-- Botones de control del panel --}}
                    <div class="mt-4 d-flex gap-2 border-top pt-3">
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                wire:click="clearSearch">
                            <i class="fas fa-eraser"></i> Limpiar búsqueda
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-secondary"
                                data-bs-dismiss="offcanvas">
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
        document.addEventListener('livewire:initialized', () => {
            {{--}}
            Livewire.on('showToastrService', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
            {{--}}

            // Fix scroll freeze after offcanvas closes
            Livewire.on('service-selected', () => {
                // Close all offcanvas
                alert('aqui');
                const offcanvasElements = document.querySelectorAll('.offcanvas.show');
                offcanvasElements.forEach(element => {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(element);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                });

                // Force remove modal-open class and restore scroll
                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Remove any backdrop that might be left
                    const backdrops = document.querySelectorAll('.offcanvas-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                }, 100);
            });
        });
    </script>
</div>
