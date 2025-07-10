<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="fas fa-palette"></i> Personalización de Tema - {{ $client->name }}
        </h4>
        <div class="card-actions">
            <button type="button" wire:click="togglePreview" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye"></i> {{ $preview_mode ? 'Desactivar' : 'Activar' }} Vista Previa
            </button>
            <button type="button" wire:click="resetToDefaults" class="btn btn-sm btn-outline-warning"
                    onclick="return confirm('¿Está seguro de restablecer el tema a los valores por defecto?')">
                <i class="fas fa-undo"></i> Restablecer
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Mensajes de estado -->
        @include('partials.message')

        <div class="row">
            <!-- Panel de Configuración -->
            <div class="col-lg-8">
                <form wire:submit.prevent="save">
                    
                    <!-- Colores Principales -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-paint-brush"></i> Colores Principales
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color Primario</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="primary_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="primary_color" class="form-control" placeholder="#3498db">
                                </div>
                                @error('primary_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color Secundario</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="secondary_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="secondary_color" class="form-control" placeholder="#2ecc71">
                                </div>
                                @error('secondary_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color de Acento</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="accent_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="accent_color" class="form-control" placeholder="#e74c3c">
                                </div>
                                @error('accent_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color de Texto</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="text_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="text_color" class="form-control" placeholder="#2c3e50">
                                </div>
                                @error('text_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Colores de Estado -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-traffic-light"></i> Colores de Estado
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Éxito</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="success_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="success_color" class="form-control" placeholder="#27ae60">
                                </div>
                                @error('success_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Advertencia</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="warning_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="warning_color" class="form-control" placeholder="#f39c12">
                                </div>
                                @error('warning_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Peligro</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="danger_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="danger_color" class="form-control" placeholder="#e74c3c">
                                </div>
                                @error('danger_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Información</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="info_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="info_color" class="form-control" placeholder="#3498db">
                                </div>
                                @error('info_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Colores de Interfaz -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-desktop"></i> Colores de Interfaz
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fondo Sidebar</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="sidebar_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="sidebar_color" class="form-control" placeholder="#34495e">
                                </div>
                                @error('sidebar_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Texto Sidebar</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="sidebar_text_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="sidebar_text_color" class="form-control" placeholder="#ffffff">
                                </div>
                                @error('sidebar_text_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Header</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="header_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="header_color" class="form-control" placeholder="#2c3e50">
                                </div>
                                @error('header_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fondo</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="background_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="background_color" class="form-control" placeholder="#ffffff">
                                </div>
                                @error('background_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bordes</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="border_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="border_color" class="form-control" placeholder="#dee2e6">
                                </div>
                                @error('border_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Colores para Documentos PDF -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-file-pdf"></i> Colores para Documentos Médicos
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Encabezado Documento</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="document_header_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="document_header_color" class="form-control" placeholder="#2c3e50">
                                </div>
                                @error('document_header_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Título Documento</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="document_title_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="document_title_color" class="form-control" placeholder="#e74c3c">
                                </div>
                                @error('document_title_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Encabezados de Tabla</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="table_header_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="table_header_color" class="form-control" placeholder="#34495e">
                                </div>
                                @error('table_header_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Diagnósticos</label>
                                <div class="color-input-group">
                                    <input type="color" wire:model.live="diagnosis_accent_color" class="form-control form-control-color">
                                    <input type="text" wire:model.live="diagnosis_accent_color" class="form-control" placeholder="#3498db">
                                </div>
                                @error('diagnosis_accent_color') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Configuraciones Adicionales -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-cog"></i> Configuraciones Adicionales
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fuente</label>
                                <select wire:model.live="font_family" class="form-control">
                                    <option value="Arial, sans-serif">Arial</option>
                                    <option value="'Helvetica Neue', Helvetica, sans-serif">Helvetica</option>
                                    <option value="'Segoe UI', Tahoma, Geneva, sans-serif">Segoe UI</option>
                                    <option value="'Roboto', sans-serif">Roboto</option>
                                    <option value="'Open Sans', sans-serif">Open Sans</option>
                                    <option value="Georgia, serif">Georgia</option>
                                    <option value="'Times New Roman', serif">Times New Roman</option>
                                </select>
                                @error('font_family') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modo Oscuro</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" wire:model.live="dark_mode" class="form-check-input" id="darkMode">
                                    <label class="form-check-label" for="darkMode">Activar modo oscuro</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CSS Personalizado -->
                    <div class="theme-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-code"></i> CSS Personalizado
                        </h5>
                        <div class="mb-3">
                            <label class="form-label">CSS Adicional (Opcional)</label>
                            <textarea wire:model.live="custom_css" class="form-control font-monospace" rows="8" 
                                      placeholder="/* Escriba CSS personalizado aquí */&#10;.custom-class {&#10;    color: #333;&#10;}"></textarea>
                            @error('custom_css') <div class="text-danger">{{ $message }}</div> @enderror
                            <small class="text-muted">Agregue CSS personalizado para estilos específicos no cubiertos por los colores básicos.</small>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="theme-section">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save"></i> Guardar Tema
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Panel de Vista Previa -->
            <div class="col-lg-4">
                <div class="preview-panel sticky-top">
                    <h5 class="section-title">
                        <i class="fas fa-eye"></i> Vista Previa
                    </h5>
                    
                    <!-- Vista Previa de Elementos -->
                    <div class="preview-container" 
                         style="{{ $this->getPreviewCssString() }}">
                        
                        <!-- Header Preview -->
                        <div class="preview-header" style="background-color: var(--header-color, {{ $header_color }}); color: white; padding: 10px; margin-bottom: 15px;">
                            <small>Header / Encabezado</small>
                        </div>

                        <!-- Sidebar Preview -->
                        <div class="preview-sidebar" style="background-color: var(--sidebar-color, {{ $sidebar_color }}); color: var(--sidebar-text-color, {{ $sidebar_text_color }}); padding: 10px; margin-bottom: 15px;">
                            <small>Sidebar / Menú Lateral</small>
                        </div>

                        <!-- Buttons Preview -->
                        <div class="mb-3">
                            <button class="btn btn-sm me-2" style="background-color: var(--primary-color, {{ $primary_color }}); border-color: var(--primary-color, {{ $primary_color }}); color: white;">
                                Primario
                            </button>
                            <button class="btn btn-sm me-2" style="background-color: var(--secondary-color, {{ $secondary_color }}); border-color: var(--secondary-color, {{ $secondary_color }}); color: white;">
                                Secundario
                            </button>
                            <button class="btn btn-sm" style="background-color: var(--accent-color, {{ $accent_color }}); border-color: var(--accent-color, {{ $accent_color }}); color: white;">
                                Acento
                            </button>
                        </div>

                        <!-- Status Colors Preview -->
                        <div class="mb-3">
                            <div class="alert alert-sm p-2 mb-2" style="background-color: {{ $success_color }}20; border-color: var(--success-color, {{ $success_color }}); color: var(--success-color, {{ $success_color }});">
                                Mensaje de éxito
                            </div>
                            <div class="alert alert-sm p-2 mb-2" style="background-color: {{ $warning_color }}20; border-color: var(--warning-color, {{ $warning_color }}); color: var(--warning-color, {{ $warning_color }});">
                                Mensaje de advertencia
                            </div>
                            <div class="alert alert-sm p-2" style="background-color: {{ $danger_color }}20; border-color: var(--danger-color, {{ $danger_color }}); color: var(--danger-color, {{ $danger_color }});">
                                Mensaje de error
                            </div>
                        </div>

                        <!-- Table Preview -->
                        <div class="table-preview">
                            <table class="table table-sm">
                                <thead style="background-color: var(--table-header-color, {{ $table_header_color }}); color: white;">
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Dosis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="color: var(--text-color, {{ $text_color }});">Ibuprofeno</td>
                                        <td style="color: var(--text-color, {{ $text_color }});">400mg</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Document Preview -->
                        <div class="document-preview mt-3">
                            <div class="document-header text-center p-2" style="border-bottom: 2px solid var(--document-header-color, {{ $document_header_color }});">
                                <div style="color: var(--document-header-color, {{ $document_header_color }}); font-weight: bold;">{{ $client->name }}</div>
                                <div style="color: var(--document-title-color, {{ $document_title_color }}); font-weight: bold; margin-top: 5px;">RECETA MÉDICA</div>
                            </div>
                        </div>

                        <!-- Typography Preview -->
                        <div class="typography-preview mt-3">
                            <p style="color: var(--text-color, {{ $text_color }}); font-family: var(--font-family, {{ $font_family }});">
                                Texto principal con la fuente seleccionada.
                            </p>
                            <small style="color: var(--text-muted-color, {{ $text_muted_color }});">
                                Texto secundario o información adicional.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
.theme-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #dee2e6;
}

.section-title {
    color: #495057;
    font-size: 1.1rem;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e9ecef;
}

.color-input-group {
    display: flex;
    gap: 8px;
}

.form-control-color {
    width: 50px;
    height: 38px;
    padding: 0;
    border: 1px solid #ced4da;
}

.preview-panel {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
}

.preview-container {
    transition: all 0.3s ease;
}

.card-actions {
    display: flex;
    gap: 10px;
}

.font-monospace {
    font-family: 'Courier New', Courier, monospace !important;
}

.alert-sm {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .color-input-group {
        flex-direction: column;
    }
    
    .form-control-color {
        width: 100%;
    }
    
    .preview-panel {
        margin-top: 20px;
        position: relative !important;
    }
}
</style>
@endpush