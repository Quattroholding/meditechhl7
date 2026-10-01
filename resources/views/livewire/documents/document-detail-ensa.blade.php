<!-- ENSA Electricity Bill Detail Card -->
<div class="card mb-6">
    <div class="card-header bg-warning-light">
        <h5 class="card-title mb-0">
            <i class="feather icon-zap"></i> Factura de Electricidad ENSA
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['invoice_number'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Número NAC</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['bill_number'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Tipo de Servicio</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['service_number'] ?? 'N/A' }}" readonly>
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
                    <label class="form-label">Dirección</label>
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
                    <label class="form-label">Vencimiento</label>
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
                    <label class="form-label">Tipo de Lectura</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['consumption_type'] ?? 'Real' }}" readonly>
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

        <hr>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card card-light">
                    <div class="card-body">
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <strong>B/. {{ number_format($billData['subtotal'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Descuentos:</span>
                                <strong>-B/. {{ number_format($billData['discounts'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        @if (($billData['taxes'] ?? 0) > 0)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Impuestos:</span>
                                <strong>B/. {{ number_format($billData['taxes'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        @endif
                        <div class="border-top pt-2 mt-2">
                            <div class="d-flex justify-content-between">
                                <span class="h6">TOTAL A PAGAR:</span>
                                <strong class="h6 text-danger">B/. {{ number_format($billData['total'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($billData['previous_balance']) && $billData['previous_balance'] != 0)
        <div class="alert alert-warning mt-3">
            <small>
                Saldo anterior: B/. {{ number_format($billData['previous_balance'] ?? 0, 2) }}
            </small>
        </div>
        @endif

        @if (isset($billData['confidence']) && $billData['confidence'] > 0)
        <div class="alert alert-info mt-2">
            <small>Confianza de extracción: {{ round($billData['confidence'] * 100) }}%</small>
        </div>
        @endif
    </div>
</div>
