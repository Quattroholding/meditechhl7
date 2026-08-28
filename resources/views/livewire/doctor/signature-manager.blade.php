<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="fas fa-signature"></i> Firma y Sello Digital
        </h4>
    </div>
    <div class="card-body">

        <!-- Mensajes de estado -->
        @include('partials.message')
        {{--}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        {{--}}

        <div class="row">
            <!-- Sección de Firma -->
            <div class="col-md-6">
                <div class="signature-section">
                    <div class="section-header mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-signature text-primary"></i> Firma Digital
                        </h5>
                        <small class="text-muted">Formato: JPG, PNG, GIF, SVG | Máximo: 2MB</small>
                    </div>

                    <!-- Firma actual -->
                    @if($existing_signature)
                        <div class="current-file mb-3">
                            <label class="form-label">Firma Actual:</label>
                            <div class="file-preview-container">
                                <img src="{{ route('practitioner.signature', $practitioner_id) }}?t={{ \Carbon\Carbon::parse($existing_signature->getRawOriginal('updated_at'))->timestamp }}"
                                     alt="Firma actual"
                                     class="signature-preview img-fluid border rounded"
                                     style="max-height: 120px; background-color: #f8f9fa;">
                                <div class="file-actions mt-2">
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirm('¿Está seguro de eliminar la firma?') || event.stopImmediatePropagation()"
                                            wire:click="deleteSignature">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Subir nueva firma -->
                    <div class="upload-section">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $existing_signature ? 'Reemplazar Firma:' : 'Subir Firma:' }}
                            </label>
                            <input type="file"
                                   wire:model="signature_file"
                                   accept="image/*"
                                   class="form-control @error('signature_file') is-invalid @enderror">
                            @error('signature_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vista previa de la nueva firma -->
                        @if($signature_preview)
                            <div class="preview-section mb-3">
                                <label class="form-label">Vista Previa:</label>
                                <div class="preview-container">
                                    <img src="{{ $signature_preview }}"
                                         alt="Vista previa de firma"
                                         class="signature-preview img-fluid border rounded"
                                         style="max-height: 120px; background-color: #f8f9fa;">
                                </div>
                            </div>
                        @endif

                        <!-- Botones de acción -->
                        <div class="action-buttons">
                            @if($signature_file)
                                <button type="button"
                                        wire:click="uploadSignature"
                                        class="btn btn-primary"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadSignature">
                                    <span wire:loading.remove wire:target="uploadSignature">
                                        <i class="fas fa-upload"></i> Subir Firma
                                    </span>
                                    <span wire:loading wire:target="uploadSignature">
                                        <i class="fas fa-spinner fa-spin"></i> Subiendo...
                                    </span>
                                </button>
                                <button type="button"
                                        wire:click="cancelSignatureUpload"
                                        class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Sello -->
            <div class="col-md-6">
                <div class="seal-section">
                    <div class="section-header mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-stamp text-success"></i> Sello Digital
                        </h5>
                        <small class="text-muted">Formato: JPG, PNG, GIF, SVG | Máximo: 2MB</small>
                    </div>

                    <!-- Sello actual -->
                    @if($existing_seal)
                        <div class="current-file mb-3">
                            <label class="form-label">Sello Actual:</label>
                            <div class="file-preview-container">
                                <img src="{{ route('practitioner.seal', $practitioner_id) }}?t={{ \Carbon\Carbon::parse($existing_seal->getRawOriginal('updated_at'))->timestamp }}"
                                     alt="Sello actual"
                                     class="seal-preview img-fluid border rounded"
                                     style="max-height: 120px; background-color: #f8f9fa;">
                                <div class="file-actions mt-2">
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirm('¿Está seguro de eliminar el sello?') || event.stopImmediatePropagation()"
                                            wire:click="deleteSeal">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Subir nuevo sello -->
                    <div class="upload-section">
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $existing_seal ? 'Reemplazar Sello:' : 'Subir Sello:' }}
                            </label>
                            <input type="file"
                                   wire:model="seal_file"
                                   accept="image/*"
                                   class="form-control @error('seal_file') is-invalid @enderror">
                            @error('seal_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vista previa del nuevo sello -->
                        @if($seal_preview)
                            <div class="preview-section mb-3">
                                <label class="form-label">Vista Previa:</label>
                                <div class="preview-container">
                                    <img src="{{ $seal_preview }}"
                                         alt="Vista previa de sello"
                                         class="seal-preview img-fluid border rounded"
                                         style="max-height: 120px; background-color: #f8f9fa;">
                                </div>
                            </div>
                        @endif

                        <!-- Botones de acción -->
                        <div class="action-buttons">
                            @if($seal_file)
                                <button type="button"
                                        wire:click="uploadSeal"
                                        class="btn btn-success"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadSeal">
                                    <span wire:loading.remove wire:target="uploadSeal">
                                        <i class="fas fa-upload"></i> Subir Sello
                                    </span>
                                    <span wire:loading wire:target="uploadSeal">
                                        <i class="fas fa-spinner fa-spin"></i> Subiendo...
                                    </span>
                                </button>
                                <button type="button"
                                        wire:click="cancelSealUpload"
                                        class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Información Importante:</h6>
                    <ul class="mb-0">
                        <li>La firma y sello se utilizarán en las recetas médicas y órdenes médicas generadas</li>
                        <li>Se recomienda usar imágenes con fondo transparente para mejor presentación</li>
                        <li>Las imágenes deben ser claras y legibles</li>
                        <li>Tamaño recomendado: 300x150 píxeles para firma, 200x200 píxeles para sello</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@push('css')
<style>
.signature-section, .seal-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    height: 100%;
}

.signature-preview, .seal-preview {
    max-width: 100%;
    max-height: 120px;
    object-fit: contain;
}

.file-preview-container {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
}

.preview-container {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 6px;
    text-align: center;
}

.section-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}

.action-buttons {
    margin-top: 15px;
}

.current-file {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
}

.file-actions {
    text-align: center;
}

.upload-section {
    margin-top: 20px;
}

@media (max-width: 768px) {
    .signature-section, .seal-section {
        margin-bottom: 20px;
    }
}
</style>
@endpush
