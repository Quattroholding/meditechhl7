<!-- Generic/Other Document Detail Card -->
<div class="card mb-6">
    <div class="card-header bg-secondary-light">
        <h5 class="card-title mb-0">
            <i class="feather icon-file"></i> Información del Proveedor
        </h5>
    </div>
    <div class="card-body">
        <!-- Supplier Information Section (For supplier invoices) -->
        @if (!empty($genericData['supplier_name']) || !empty($genericData['supplier_ruc']) || !empty($genericData['supplier_dv']))
        <div class="card mb-4 border-primary">

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre del Proveedor</label>
                        <input type="text"
                            wire:model="genericData.supplier_name"
                            class="form-control"
                            placeholder="Nombre del proveedor"
                            @disabled($isApproving)>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">RUC</label>
                        <input type="text"
                            wire:model="genericData.supplier_ruc"
                            class="form-control"
                            placeholder="RUC (12 dígitos)"
                            @disabled($isApproving)>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">DV</label>
                        <input type="text"
                            wire:model="genericData.supplier_dv"
                            class="form-control"
                            placeholder="Dígito Verificador"
                            maxlength="1"
                            @disabled($isApproving)>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Teléfono del Proveedor (Opcional)</label>
                        <input type="text"
                            wire:model="genericData.supplier_phone"
                            class="form-control"
                            placeholder="Teléfono"
                            @disabled($isApproving)>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <hr>

        <!-- Key-Value Pairs Section -->
        @if (!empty($editableFields['key_value_pairs']))
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Pares Clave-Valor Detectados</h6>
            </div>
            <div class="card-body">
                @foreach($editableFields['key_value_pairs'] ?? [] as $index => $pair)
                <div class="row mb-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text"
                            wire:model="editableFields.key_value_pairs.{{ $index }}.key"
                            class="form-control form-control-sm"
                            placeholder="Clave"
                            @disabled($isApproving)>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            wire:model="editableFields.key_value_pairs.{{ $index }}.value"
                            class="form-control form-control-sm"
                            placeholder="Valor"
                            @disabled($isApproving)>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Numeric Fields Section -->
        @if (!empty($editableFields['numeric_fields']))
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Campos Numéricos Detectados</h6>
            </div>
            <div class="card-body">
                @foreach($editableFields['numeric_fields'] ?? [] as $index => $field)
                <div class="row mb-2 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label mb-0">{{ $field['label'] ?? "Campo $index" }}</label>
                    </div>
                    <div class="col-md-8">
                        <input type="number"
                            step="0.01"
                            wire:model="editableFields.numeric_fields.{{ $index }}.value"
                            class="form-control form-control-sm"
                            @disabled($isApproving)>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tables Section -->
        @foreach($editableFields['tables'] ?? [] as $tableIndex => $table)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Tabla {{ $tableIndex + 1 }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        @foreach($table as $rowIndex => $row)
                        <tr>
                            @foreach($row as $cellIndex => $cell)
                            <td>
                                <input type="text"
                                    wire:model="editableFields.tables.{{ $tableIndex }}.{{ $rowIndex }}.{{ $cellIndex }}"
                                    class="form-control form-control-sm"
                                    @disabled($isApproving)>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Full Text Section (Collapsible) -->
        @if (!empty($editableFields['full_text']))
        <div class="card mb-3">
            <div class="card-header bg-light">
                <a class="text-decoration-none" data-bs-toggle="collapse" href="#fullTextCollapse">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-chevron-down"></i> Ver texto completo extraído
                    </h6>
                </a>
            </div>
            <div id="fullTextCollapse" class="collapse">
                <div class="card-body">
                    <textarea wire:model="editableFields.full_text"
                        rows="12"
                        class="form-control font-monospace"
                        style="font-size: 0.85rem;"
                        @disabled($isApproving)></textarea>
                </div>
            </div>
        </div>
        @endif

        @if (isset($genericData['confidence']) && $genericData['confidence'] > 0)
        <div class="alert alert-info mt-3">
            <small>Confianza de extracción: {{ round($genericData['confidence'] * 100) }}%</small>
        </div>
        @endif
    </div>
</div>
