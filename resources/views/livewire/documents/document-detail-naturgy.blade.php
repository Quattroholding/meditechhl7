<!-- Naturgy Electricity Bill Detail Card -->
<div class="card mb-6">
    <div class="card-header bg-info-light">
        <h5 class="card-title mb-0">
            <i class="feather icon-zap"></i> Factura de Electricidad Naturgy
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['bill_number'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Número NIS</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['nis_number'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Medidor</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['meter_number'] ?? 'N/A' }}" readonly>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['customer_name'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Dirección de Servicio</label>
                    <textarea class="form-control form-control-plaintext" readonly rows="2">{{ $billData['service_address'] ?? 'N/A' }}</textarea>
                </div>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Periodo Inicio</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['billing_period_start'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Periodo Fin</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['billing_period_end'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Fecha Emisión</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['issue_date'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Fecha Vencimiento</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['due_date'] ?? 'N/A' }}" readonly>
                </div>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Consumo (kWh)</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ number_format($billData['consumption_kwh'] ?? 0, 0) }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Demanda (kW)</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ number_format($billData['demand_kw'] ?? 0, 2) }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Proveedor</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="Naturgy (Edemet-Edechi)" readonly>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card border-info">
                    <div class="card-header bg-info-light">
                        <h6 class="card-title mb-0">Desglose del Monto</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total a Pagar:</span>
                                <strong class="text-primary">B/. {{ number_format($billData['total_amount'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($billData['confidence']) && $billData['confidence'] > 0)
        <div class="alert alert-info mt-3">
            <small>Confianza de extracción: {{ round($billData['confidence'] * 100) }}%</small>
        </div>
        @endif
    </div>
</div>
