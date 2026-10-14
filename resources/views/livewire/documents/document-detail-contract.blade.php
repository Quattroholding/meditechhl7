<!-- Contract Detail Card -->
<div class="card mb-6">
    <div class="card-header bg-info-light">
        <h5 class="card-title mb-0">
            <i class="feather icon-file-text"></i> Información del Contrato
        </h5>
    </div>
    <div class="card-body">

        <!-- Contract Identification Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo de Contrato</label>
                    <input type="text" class="form-control"
                        wire:model="contractData.contract_type"
                        placeholder="Tipo de contrato"
                        @disabled($isApproving)>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título del Contrato</label>
                    <input type="text" class="form-control"
                        wire:model="contractData.contract_title"
                        placeholder="Título del contrato"
                        @disabled($isApproving)>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Número de Contrato</label>
                    <input type="text" class="form-control"
                        wire:model="contractData.contract_number"
                        placeholder="Número de identificación"
                        @disabled($isApproving)>
                </div>
            </div>
        </div>

        <hr>

        <!-- Parties Section -->
        <h6 class="fw-semibold mb-3">Partes Involucradas</h6>
        <div class="card mb-4 border-primary">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 mb-2">
                        <small class="text-muted">PRIMERA PARTE (Proveedor/Arrendador)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control"
                                wire:model="contractData.party_1_name"
                                placeholder="Nombre de la primera parte"
                                @disabled($isApproving)>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" wire:model="contractData.party_1_type" @disabled($isApproving)>
                                <option value="">-- Selecciona --</option>
                                <option value="individual">Persona Natural</option>
                                <option value="legal_entity">Entidad Legal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Cédula/RUC</label>
                            <input type="text" class="form-control"
                                wire:model="contractData.party_1_id"
                                placeholder="Identificación"
                                @disabled($isApproving)>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border-success">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12 mb-2">
                        <small class="text-muted">SEGUNDA PARTE (Cliente/Arrendatario)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control"
                                wire:model="contractData.party_2_name"
                                placeholder="Nombre de la segunda parte"
                                @disabled($isApproving)>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" wire:model="contractData.party_2_type" @disabled($isApproving)>
                                <option value="">-- Selecciona --</option>
                                <option value="individual">Persona Natural</option>
                                <option value="legal_entity">Entidad Legal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Cédula/RUC</label>
                            <input type="text" class="form-control"
                                wire:model="contractData.party_2_id"
                                placeholder="Identificación"
                                @disabled($isApproving)>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <!-- Dates Section -->
        <h6 class="fw-semibold mb-3">Fechas del Contrato</h6>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control"
                        wire:model="contractData.start_date"
                        @disabled($isApproving)>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Fecha Finalización</label>
                    <input type="date" class="form-control"
                        wire:model="contractData.end_date"
                        @disabled($isApproving)>
                </div>
            </div>
        </div>

        @if (!empty($contractData['signature_dates']))
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Fecha de Firma</label>
                    <input type="date" class="form-control"
                        wire:model="contractData.signature_dates"
                        @disabled($isApproving)>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Firmas Presentes</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input"
                            wire:model="contractData.signatures_present"
                            @disabled($isApproving) id="signaturesCheck">
                        <label class="form-check-label" for="signaturesCheck">
                            Sí, contiene firmas
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <hr>

        <!-- Property Information (for rental contracts) -->
        @if (!empty($contractData['property_address']) || !empty($contractData['property_type']))
        <h6 class="fw-semibold mb-3">Información de la Propiedad</h6>
        <div class="row mb-4">
            @if (!empty($contractData['property_address']))
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea class="form-control" rows="2"
                        wire:model="contractData.property_address"
                        placeholder="Dirección de la propiedad"
                        @disabled($isApproving)></textarea>
                </div>
            </div>
            @endif
            @if (!empty($contractData['property_type']))
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Tipo de Propiedad</label>
                    <input type="text" class="form-control"
                        wire:model="contractData.property_type"
                        placeholder="Tipo de propiedad"
                        @disabled($isApproving)>
                </div>
            </div>
            @endif
        </div>
        <hr>
        @endif

        <!-- Payment Terms Section -->
        @if (!empty($contractData['contract_value']) || !empty($contractData['payment_amount']))
        <h6 class="fw-semibold mb-3">Términos de Pago</h6>
        <div class="row mb-4">
            @if (!empty($contractData['contract_value']))
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Valor Total del Contrato</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <select class="form-select form-select-sm" wire:model="contractData.currency" style="max-width: 100px;" @disabled($isApproving)>
                                <option value="PAB">PAB</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="contractData.contract_value"
                                placeholder="0.00"
                                @disabled($isApproving)>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($contractData['payment_amount']))
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Monto Periódico</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <select class="form-select form-select-sm" wire:model="contractData.currency" style="max-width: 100px;" @disabled($isApproving)>
                                <option value="PAB">PAB</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                            <input type="number" step="0.01" class="form-control form-control-lg text-end"
                                wire:model="contractData.payment_amount"
                                placeholder="0.00"
                                @disabled($isApproving)>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($contractData['payment_frequency']))
            <div class="col-md-4">
                <div class="card border-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Frecuencia de Pago</h6>
                    </div>
                    <div class="card-body">
                        <select class="form-select" wire:model="contractData.payment_frequency"  @disabled($isApproving)>
                            <option value="">-- Selecciona --</option>
                            <option value="weekly">SEMENAL</option>
                            <option value="monthly">MENSUAL</option>
                            <option value="yearly">ANUAL</option>
                        </select>

                    </div>
                </div>
            </div>
            @endif
        </div>
        <hr>
        @endif

        <!-- Contract Terms Section -->
        @if (!empty($contractData['renewal_terms']) || !empty($contractData['termination_conditions']))
        <h6 class="fw-semibold mb-3">Términos y Condiciones</h6>
        <div class="row mb-4">
            @if (!empty($contractData['renewal_terms']))
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Renovación</label>
                    <input type="text" class="form-control"
                        wire:model="contractData.renewal_terms"
                        placeholder="Términos de renovación"
                        @disabled($isApproving)>
                </div>
            </div>
            @endif
            @if (!empty($contractData['termination_conditions']))
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Terminación</label>
                    <textarea class="form-control" rows="2"
                        wire:model="contractData.termination_conditions"
                        placeholder="Condiciones de terminación"
                        @disabled($isApproving)></textarea>
                </div>
            </div>
            @endif
        </div>
        @if (!empty($contractData['early_termination_penalty']))
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Penalidad por Terminación Anticipada</label>
                    <textarea class="form-control" rows="2"
                        wire:model="contractData.early_termination_penalty"
                        placeholder="Penalidad o multa"
                        @disabled($isApproving)></textarea>
                </div>
            </div>
        </div>
        @endif
        <hr>
        @endif

        <!-- Key Clauses Section -->
        @if (!empty($contractData['key_clauses']) && count($contractData['key_clauses']) > 0)
        <h6 class="fw-semibold mb-3">Cláusulas Principales</h6>
        <div class="card mb-4">
            <div class="card-body">
                <ol class="mb-0">
                    @foreach($contractData['key_clauses'] as $index => $clause)
                    <li class="mb-2">
                        <small>{{ $clause }}</small>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
        <hr>
        @endif

        <!-- Special Conditions Section -->
        @if (!empty($contractData['special_conditions']))
        <h6 class="fw-semibold mb-3">Condiciones Especiales</h6>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-warning-light">
                    <div class="card-body">
                        <p class="mb-0">{{ $contractData['special_conditions'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @endif

        <!-- Confidence & Metadata -->
        @if (isset($contractData['confidence']) && $contractData['confidence'] > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Confianza de Extracción</label>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ ($contractData['confidence'] ?? 0) * 100 }}%;"
                            aria-valuenow="{{ ($contractData['confidence'] ?? 0) * 100 }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            {{ round(($contractData['confidence'] ?? 0) * 100) }}%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if (!empty($contractData['model_used']))
                <div class="alert alert-info mb-0">
                    <small><strong>Modelo:</strong> {{ $contractData['model_used'] }}</small><br>
                    <small><strong>Tokens:</strong> {{ $contractData['input_tokens'] ?? 0 }} in / {{ $contractData['output_tokens'] ?? 0 }} out</small>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
