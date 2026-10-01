<!-- NATURGY Gas Bill Detail Card -->
<div class="card mb-6">
    <div class="card-header bg-danger-light">
        <h5 class="card-title mb-0">
            <i class="feather icon-flame"></i> Factura de Gas NATURGY
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Número de Factura</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['bill_number'] ?? 'N/A' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control form-control-plaintext"
                        value="{{ $billData['customer_name'] ?? 'N/A' }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Dirección de Servicio</label>
                    <textarea class="form-control form-control-plaintext" readonly rows="3">{{ $billData['service_address'] ?? 'N/A' }}</textarea>
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
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Datos de Consumo</label>
                    <textarea class="form-control form-control-plaintext" readonly rows="3">{{ json_encode($billData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3 offset-md-6">
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <strong>B/. {{ number_format($billData['subtotal'] ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-2">
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span>Total:</span>
                        <strong class="text-danger">B/. {{ number_format($billData['total'] ?? 0, 2) }}</strong>
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
