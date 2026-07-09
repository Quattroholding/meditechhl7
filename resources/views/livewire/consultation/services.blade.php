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

            {{-- SELECTOR VISUAL POR TIPO DE SERVICIO --}}
            <div style="padding: 20px;">
                <h6 class="mb-3"><i class="fas fa-hand-pointer"></i> Seleccione el tipo de servicio:</h6>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px;">
                    {{-- Consulta --}}
                    @if(isset($availableServiceTypes['consultation']) && $availableServiceTypes['consultation'] > 0)
                    <div wire:click="$set('selectedServiceType', 'consultation')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'consultation' ? '#007bff' : 'white' }}; color: {{ $selectedServiceType === 'consultation' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'consultation' ? '#007bff' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'consultation' ? '0 4px 8px rgba(0,123,255,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#007bff'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'consultation') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-user-md" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Consulta</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['consultation'] }})</small>
                    </div>
                    @endif

                    {{-- Procedimiento --}}
                    @if(isset($availableServiceTypes['procedure']) && $availableServiceTypes['procedure'] > 0)
                    <div wire:click="$set('selectedServiceType', 'procedure')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'procedure' ? '#28a745' : 'white' }}; color: {{ $selectedServiceType === 'procedure' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'procedure' ? '#28a745' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'procedure' ? '0 4px 8px rgba(40,167,69,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#28a745'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'procedure') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-procedures" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Procedimiento</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['procedure'] }})</small>
                    </div>
                    @endif

                    {{-- Terapéutico --}}
                    @if(isset($availableServiceTypes['therapeutic']) && $availableServiceTypes['therapeutic'] > 0)
                    <div wire:click="$set('selectedServiceType', 'therapeutic')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'therapeutic' ? '#e83e8c' : 'white' }}; color: {{ $selectedServiceType === 'therapeutic' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'therapeutic' ? '#e83e8c' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'therapeutic' ? '0 4px 8px rgba(232,62,140,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#e83e8c'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'therapeutic') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-heartbeat" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Terapéutico</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['therapeutic'] }})</small>
                    </div>
                    @endif

                    {{-- Quirúrgico --}}
                    @if(isset($availableServiceTypes['surgical']) && $availableServiceTypes['surgical'] > 0)
                    <div wire:click="$set('selectedServiceType', 'surgical')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'surgical' ? '#dc3545' : 'white' }}; color: {{ $selectedServiceType === 'surgical' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'surgical' ? '#dc3545' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'surgical' ? '0 4px 8px rgba(220,53,69,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#dc3545'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'surgical') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-cut" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Quirúrgico</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['surgical'] }})</small>
                    </div>
                    @endif

                    {{-- Laboratorio --}}
                    @if(isset($availableServiceTypes['laboratory']) && $availableServiceTypes['laboratory'] > 0)
                    <div wire:click="$set('selectedServiceType', 'laboratory')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'laboratory' ? '#6f42c1' : 'white' }}; color: {{ $selectedServiceType === 'laboratory' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'laboratory' ? '#6f42c1' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'laboratory' ? '0 4px 8px rgba(111,66,193,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#6f42c1'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'laboratory') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-flask" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Laboratorio</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['laboratory'] }})</small>
                    </div>
                    @endif

                    {{-- Imagenología --}}
                    @if(isset($availableServiceTypes['imaging']) && $availableServiceTypes['imaging'] > 0)
                    <div wire:click="$set('selectedServiceType', 'imaging')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'imaging' ? '#17a2b8' : 'white' }}; color: {{ $selectedServiceType === 'imaging' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'imaging' ? '#17a2b8' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'imaging' ? '0 4px 8px rgba(23,162,184,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#17a2b8'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'imaging') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-x-ray" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Imagenología</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['imaging'] }})</small>
                    </div>
                    @endif

                    {{-- Suministro --}}
                    @if(isset($availableServiceTypes['supply']) && $availableServiceTypes['supply'] > 0)
                    <div wire:click="$set('selectedServiceType', 'supply')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'supply' ? '#fd7e14' : 'white' }}; color: {{ $selectedServiceType === 'supply' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'supply' ? '#fd7e14' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'supply' ? '0 4px 8px rgba(253,126,20,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#fd7e14'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'supply') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-box" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Suministro</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['supply'] }})</small>
                    </div>
                    @endif

                    {{-- Otro --}}
                    @if(isset($availableServiceTypes['other']) && $availableServiceTypes['other'] > 0)
                    <div wire:click="$set('selectedServiceType', 'other')"
                         style="cursor: pointer; padding: 15px 10px; background: {{ $selectedServiceType === 'other' ? '#6c757d' : 'white' }}; color: {{ $selectedServiceType === 'other' ? 'white' : '#212529' }}; border: 2px solid {{ $selectedServiceType === 'other' ? '#6c757d' : '#dee2e6' }}; border-radius: 12px; text-align: center; transition: all 0.2s; box-shadow: {{ $selectedServiceType === 'other' ? '0 4px 8px rgba(108,117,125,0.3)' : 'none' }};"
                         onmouseover="if(this.style.background === 'white') { this.style.background='#f8f9fa'; this.style.borderColor='#6c757d'; this.style.transform='translateY(-2px)'; }"
                         onmouseout="if('{{ $selectedServiceType }}' !== 'other') { this.style.background='white'; this.style.borderColor='#dee2e6'; this.style.transform='translateY(0)'; }">
                        <i class="fas fa-stethoscope" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                        <div style="font-weight: 600; font-size: 0.8rem;">Otro</div>
                        <small style="opacity: 0.8; font-size: 0.7rem;">({{ $availableServiceTypes['other'] }})</small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- VERSIÓN ANTERIOR CON BUSCADOR (COMENTADA) --}}
            {{--
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
            --}}
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

            {{-- RESULTADOS DE BÚSQUEDA - VERSIÓN MEJORADA CON ÍCONOS --}}

            @if(!empty($results))
                <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    {{-- Contenedor con scroll --}}
                    <div style="max-height: 400px; min-height: 200px; overflow-y: auto;padding: 0 0 150px 0;">
                        @foreach($results as $result)
                            @php
                                // Definir ícono y color según el tipo de servicio
                                $iconConfig = match($result['service_type'] ?? 'other') {
                                    'consultation' => ['icon' => 'fa-user-md', 'color' => '#007bff', 'label' => 'Consulta'],
                                    'procedure' => ['icon' => 'fa-procedures', 'color' => '#28a745', 'label' => 'Procedimiento'],
                                    'therapy' => ['icon' => 'fa-heartbeat', 'color' => '#e83e8c', 'label' => 'Terapia'],
                                    'surgical' => ['icon' => 'fa-cut', 'color' => '#dc3545', 'label' => 'Quirúrgico'],
                                    'laboratory' => ['icon' => 'fa-flask', 'color' => '#6f42c1', 'label' => 'Laboratorio'],
                                    'imaging' => ['icon' => 'fa-x-ray', 'color' => '#17a2b8', 'label' => 'Imágenes'],
                                    'diagnostic' => ['icon' => 'fa-file-medical-alt', 'color' => '#fd7e14', 'label' => 'Diagnóstico'],
                                    default => ['icon' => 'fa-stethoscope', 'color' => '#6c757d', 'label' => 'Servicio']
                                };
                                $hasCpt = !empty($result['cpt_code']);
                            @endphp
                            <div
                                class="sel-list-item"
                                wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                                x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'diagnostic-search' }))"
                                style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: all 0.2s; display: flex; align-items: start; gap: 12px;"
                                onmouseover="this.style.background='#f0f7ff'; this.style.borderLeft='4px solid {{ $iconConfig['color'] }}'"
                                onmouseout="this.style.background='white'; this.style.borderLeft='none'"
                            >
                                {{-- Ícono del tipo de servicio --}}
                                <div style="flex-shrink: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: {{ $iconConfig['color'] }}15; border-radius: 8px;">
                                    <i class="fas {{ $iconConfig['icon'] }}" style="font-size: 1.2rem; color: {{ $iconConfig['color'] }};"></i>
                                </div>

                                {{-- Información del servicio --}}
                                <div style="flex: 1; min-width: 0;">
                                    {{-- Nombre del servicio --}}
                                    <div style="font-size: 0.9rem; color: #212529; font-weight: 600; margin-bottom: 4px; line-height: 1.3;">
                                        {{ $result['name'] }}
                                    </div>

                                    {{-- Badges y metadata --}}
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-bottom: 4px;">
                                        {{-- Badge de tipo de servicio --}}
                                        <span style="display: inline-flex; align-items: center; padding: 2px 8px; background: {{ $iconConfig['color'] }}20; color: {{ $iconConfig['color'] }}; border-radius: 12px; font-size: 0.7rem; font-weight: 500;">
                                            {{ $iconConfig['label'] }}
                                        </span>

                                        {{-- Badge CPT si existe --}}
                                        @if($hasCpt)
                                            <span class="badge bg-primary" style="font-size: 0.7rem; padding: 2px 8px;">
                                                CPT: {{ $result['cpt_code'] }}
                                            </span>
                                        @endif

                                        {{-- Duración si existe --}}
                                        @if(!empty($result['duration_minutes']))
                                            <span style="font-size: 0.75rem; color: #6c757d;">
                                                <i class="far fa-clock"></i> {{ $result['duration_minutes'] }} min
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Descripción si existe y es diferente del nombre --}}
                                    @if(!empty($result['description']) && $result['description'] !== $result['name'])
                                        <div style="font-size: 0.75rem; color: #6c757d; line-height: 1.3; margin-top: 2px;">
                                            {{ Str::limit($result['description'], 80) }}
                                        </div>
                                    @endif

                                    {{-- Precio --}}
                                    <div style="margin-top: 6px;">
                                        <span style="font-size: 0.85rem; font-weight: 600; color: #28a745;">
                                            <i class="fas fa-dollar-sign" style="font-size: 0.7rem;"></i> {{ number_format($result['price'], 2) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Botón de acción --}}
                                <div style="flex-shrink: 0; display: flex; align-items: center;">
                                    <div style="padding: 6px 12px; background: {{ $iconConfig['color'] }}; color: white; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-plus"></i> Agregar
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- VERSIÓN ANTERIOR COMENTADA (lista simple) --}}
            {{--
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
                    @endforeach
                </div>
            @endif
            --}}
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
