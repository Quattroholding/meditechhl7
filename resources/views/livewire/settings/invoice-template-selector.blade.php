<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Seleccionar Plantilla de Factura</h4>
                    <p class="small mb-0 text-warning">Elige el diseño que se usará para tus facturas</p>
                </div>
                <div class="card-body">
                    @if(count($availableTemplates) === 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay plantillas de factura disponibles en este momento.
                        </div>
                    @else
                        <div class="row">
                            @foreach($availableTemplates as $template)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card template-card h-100 {{ $selectedTemplate === $template['name'] ? 'border-primary shadow-sm' : '' }}"
                                         style="cursor: pointer; transition: all 0.3s ease;">

                                        <!-- Preview iframe -->
                                        <div class="card-img-top bg-light position-relative overflow-hidden"
                                             style="height: 300px;"
                                             wire:click="selectTemplate('{{ $template['name'] }}')">

                                            @if($selectedTemplate === $template['name'])
                                                <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-check me-1"></i>Seleccionada
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Vista previa en miniatura -->
                                            <div class="template-preview-wrapper" style="width: 100%; height: 100%; transform: scale(0.35); transform-origin: top left; pointer-events: none;">
                                                <iframe
                                                    src="{{ route('setting.invoice_template.preview', $template['name']) }}"
                                                    style="width: 285%; height: 285%; border: none; background: white;"
                                                    scrolling="no"
                                                    loading="lazy"
                                                ></iframe>
                                            </div>

                                            <!-- Overlay for hover effect -->
                                            <div class="preview-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                 style="background: rgba(0,0,0,0); transition: background 0.3s ease;">
                                                <button class="btn btn-light btn-sm preview-view-btn" style="opacity: 0; transition: opacity 0.3s ease;">
                                                    <i class="fas fa-eye me-1"></i>Ver Completo
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-2">{{ $template['label'] }}</h6>
                                            <p class="card-text small text-muted mb-3">
                                                Diseño profesional {{ $template['number'] }}
                                            </p>

                                            @if($selectedTemplate === $template['name'])
                                                <button class="btn btn-primary btn-sm w-100" disabled>
                                                    <i class="fas fa-check me-1"></i>Seleccionada
                                                </button>
                                            @else
                                                <button class="btn btn-outline-primary btn-sm w-100"
                                                        wire:click.stop="selectTemplate('{{ $template['name'] }}')">
                                                    <i class="fas fa-check me-1"></i>Seleccionar
                                                </button>
                                            @endif

                                            <!-- Link para ver en modal -->
                                            <button class="btn btn-link btn-sm w-100 mt-1"
                                                    wire:click.stop="openPreviewModal('{{ $template['name'] }}', '{{ $template['label'] }}')">
                                                <i class="fas fa-search-plus me-1"></i>Vista Previa Completa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{--}}
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> La plantilla seleccionada se aplicará a todas las nuevas facturas generadas.
                            Las facturas existentes mantendrán su diseño original.
                        </div>
                        {{--}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Full Preview -->
    @if($showPreviewModal)
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $previewTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closePreviewModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" style="height: 80vh;">
                        <iframe src="{{ $previewUrl }}" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePreviewModal">Cerrar</button>
                    </div>
                </div>

        </div>
    @endif
    <style>
        .template-card {
            transition: all 0.3s ease;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }

        .template-card:hover .preview-overlay {
            background: rgba(0,0,0,0.3) !important;
        }

        .template-card:hover .preview-view-btn {
            opacity: 1 !important;
        }

        .template-card.border-primary {
            border-width: 2px !important;
        }

        .template-preview-wrapper {
            position: relative;
        }

        .template-card .card-img-top {
            cursor: pointer;
        }

        /* Loading state for iframes */
        .template-preview-wrapper iframe {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</div>



@script
<script>
    $wire.on('success', (message) => {
        toastr.success(message[0] || message);
    });

    $wire.on('error', (message) => {
        toastr.error(message[0] || message);
    });
</script>
@endscript
