<!-- IA Document Detail Card - Editable Form -->
<div class="card mb-6">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="feather icon-cpu"></i> Información Extraída por IA
        </h5>
    </div>
    <div class="card-body">
        <!-- Provider & Service Type (always show) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Proveedor</label>
                    <input type="text" class="form-control"
                        wire:model="billData.provider_name"
                        placeholder="Nombre del proveedor">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tipo de Servicio</label>
                    <select class="form-select" wire:model="billData.service_type">
                        <option value="">-- Selecciona tipo --</option>
                        <option value="electricity">Electricidad</option>
                        <option value="water">Agua</option>
                        <option value="gas">Gas</option>
                        <option value="internet">Internet</option>
                        <option value="phone">Teléfono</option>
                        <option value="other">Otro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bill Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" class="form-control"
                        wire:model="billData.bill_number"
                        placeholder="Número de factura">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control"
                        wire:model="billData.customer_name"
                        placeholder="Nombre del cliente">
                </div>
            </div>
        </div>

        <!-- Address & Meter (show address always, meter only if present) -->
        <div class="row mb-4">
            <div class="col-md-{{ !empty($billData['meter_number']) ? '6' : '12' }}">
                <div class="mb-3">
                    <label class="form-label">Dirección de Servicio</label>
                    <textarea class="form-control" rows="2"
                        wire:model="billData.service_address"
                        placeholder="Dirección de servicio"></textarea>
                </div>
            </div>
            @if (!empty($billData['meter_number']))
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Número de Medidor</label>
                    <input type="text" class="form-control"
                        wire:model="billData.meter_number"
                        placeholder="Número de medidor">
                </div>
            </div>
            @endif
        </div>

        <hr>

        <!-- Dates (only show if present) -->
        @if (!empty($billData['issue_date']) || !empty($billData['due_date']) || !empty($billData['billing_period_start']) || !empty($billData['billing_period_end']))
        <div class="row mb-4">
            @if (!empty($billData['billing_period_start']))
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Período Inicio</label>
                    <input type="date" class="form-control"
                        wire:model="billData.billing_period_start">
                </div>
            </div>
            @endif
            @if (!empty($billData['billing_period_end']))
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Período Fin</label>
                    <input type="date" class="form-control"
                        wire:model="billData.billing_period_end">
                </div>
            </div>
            @endif
            @if (!empty($billData['issue_date']))
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Fecha Emisión</label>
                    <input type="date" class="form-control"
                        wire:model="billData.issue_date">
                </div>
            </div>
            @endif
            @if (!empty($billData['due_date']))
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Fecha Vencimiento</label>
                    <input type="date" class="form-control"
                        wire:model="billData.due_date">
                </div>
            </div>
            @endif
        </div>

        <hr>
        @endif

        <!-- Consumption & Demand (only show relevant fields for service type) -->
        @if (!empty($billData['consumption_value']) || !empty($billData['consumption_kwh']) || !empty($billData['consumption_cubic_meters']) || !empty($billData['demand_kw']))
        <div class="row mb-4">
            @if (!empty($billData['consumption_value']) && !empty($billData['consumption_unit']))
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Consumo</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control"
                            wire:model="billData.consumption_value"
                            placeholder="Cantidad">
                        <span class="input-group-text">
                            <input type="text" style="border: none; width: 80px;"
                                wire:model="billData.consumption_unit"
                                placeholder="kWh">
                        </span>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($billData['consumption_kwh']))
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Consumo kWh</label>
                    <input type="number" step="0.01" class="form-control"
                        wire:model="billData.consumption_kwh"
                        placeholder="0.00">
                </div>
            </div>
            @endif
            @if (!empty($billData['consumption_cubic_meters']))
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Consumo m³</label>
                    <input type="number" step="0.01" class="form-control"
                        wire:model="billData.consumption_cubic_meters"
                        placeholder="0.00">
                </div>
            </div>
            @endif
            @if (!empty($billData['demand_kw']))
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Demanda kW</label>
                    <input type="number" step="0.01" class="form-control"
                        wire:model="billData.demand_kw"
                        placeholder="0.00">
                </div>
            </div>
            @endif
        </div>

        <hr>
        @endif

        <!-- Amount Details -->
        <div class="row mb-4">
            @if (!empty($billData['subtotal_amount']))
            <div class="col-md-6">
                <div class="card border-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Subtotal</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">B/.</span>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="billData.subtotal_amount"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($billData['itbms_amount']))
            <div class="col-md-6">
                <div class="card border-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">ITBMS/Impuesto</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">B/.</span>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="billData.itbms_amount"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if (!empty($billData['other_taxes']))
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Otros Impuestos</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">B/.</span>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="billData.other_taxes"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <div class="card border-purple">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Monto Total a Pagar</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">B/.</span>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="billData.total_amount"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confidence & Notes -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Confianza de Extracción</label>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ ($billData['confidence'] ?? 0) * 100 }}%;"
                            aria-valuenow="{{ ($billData['confidence'] ?? 0) * 100 }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            {{ round(($billData['confidence'] ?? 0) * 100) }}%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Notas (Opcional)</label>
                    <input type="text" class="form-control"
                        wire:model="notes"
                        placeholder="Añade notas sobre este documento...">
                </div>
            </div>
        </div>

        <!-- Additional Fields Info -->
        @if (isset($billData) && count($billData) > 0)
        <div class="alert alert-info mt-3">
            <small>
                <strong>Campos deducidos por IA:</strong>
                {{ count(array_filter($billData, function($v) { return !is_null($v) && $v !== ''; })) }}
                de {{ count($billData) }} campos
            </small>
        </div>
        @endif

    </div>
</div>
