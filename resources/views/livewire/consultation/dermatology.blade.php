<div class="dermatology-section">
    {{-- Flash Messages --}}
    {{--}}@if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif {{--}}

    <div class="row">
        {{-- Left Side: Body Diagram --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-injured me-2"></i>
                        Mapa Corporal de Lesiones
                    </h5>
                </div>
                <div class="card-body">
                    {{-- View Tabs --}}
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button"
                                wire:click="changeView('anterior')"
                                class="btn {{ $bodyView === 'anterior' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-male"></i> Anterior
                        </button>
                        <button type="button"
                                wire:click="changeView('posterior')"
                                class="btn {{ $bodyView === 'posterior' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-male fa-rotate-180"></i> Posterior
                        </button>
                        <button type="button"
                                wire:click="changeView('left_lateral')"
                                class="btn {{ $bodyView === 'left_lateral' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-male fa-rotate-270"></i> Lateral Izq.
                        </button>
                        <button type="button"
                                wire:click="changeView('right_lateral')"
                                class="btn {{ $bodyView === 'right_lateral' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-male fa-rotate-90"></i> Lateral Der.
                        </button>
                    </div>

                    {{-- Body Diagram Container --}}
                    <div class="body-diagram-container" style="position: relative; width: 100%; max-width: 500px; margin: 0 auto; overflow: hidden;">
                        @php
                            $gender = strtolower($patient->gender ?? 'male');
                            // Handle both 'femenino'/'female' and 'masculino'/'male' values
                            $genderSuffix = in_array($gender, ['femenino', 'female']) ? 'female' : 'male';
                            // Determine image based on view
                            if ($bodyView === 'anterior') {
                                $imageUrl = asset("assets/img/body/anterior_{$genderSuffix}.png");
                            } elseif ($bodyView === 'posterior') {
                                $imageUrl = asset("assets/img/body/posterior_{$genderSuffix}.png");
                            } elseif ($bodyView === 'left_lateral') {
                                $imageUrl = asset("assets/img/body/lateral_left_{$genderSuffix}.png");
                            } elseif ($bodyView === 'right_lateral') {

                                $imageUrl = asset("assets/img/body/lateral_right_{$genderSuffix}.png");
                            } else {
                                // Fallback to anterior if view is not recognized
                                $imageUrl = asset("assets/img/body/anterior_{$genderSuffix}.png");
                            }
                        @endphp

                        {{-- Body Diagram Image --}}
                        <div id="bodyDiagram" style="position: relative; width: 100%; cursor: crosshair;  overflow: hidden; ">
                            <img src="{{ $imageUrl }}"
                                 alt="Diagrama corporal - {{ $bodyView }}"
                                 style="width: 100%; height: auto; display: block; user-select: none;"
                                 draggable="false">
                        </div>

                        {{-- Markers Overlay --}}
                        <div class="markers-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                            @foreach($markings as $marking)
                                <div class="lesion-marker"
                                     data-lesion-id="{{ $marking['lesion_id'] }}"
                                     style="position: absolute;
                                            left: {{ $marking['x'] }}%;
                                            top: {{ $marking['y'] }}%;
                                            transform: translate(-50%, -50%);
                                            width: {{ $marking['size'] }}px;
                                            height: {{ $marking['size'] }}px;
                                            background-color: {{ $marking['color'] }};
                                            border: 2px solid white;
                                            pointer-events: all;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                                            transition: all 0.2s;
                                            @if($marking['shape'] === 'circle')
                                                border-radius: 50%;
                                            @elseif($marking['shape'] === 'square')
                                                border-radius: 0;
                                            @elseif($marking['shape'] === 'triangle')
                                                border-radius: 0;
                                                clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
                                            @elseif($marking['shape'] === 'star')
                                                border-radius: 0;
                                                clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
                                            @endif"
                                     title="{{ $marking['label'] ?? 'Lesión' }} - {{ ucfirst($marking['risk_level']) }}">
                                    @if($marking['label'])
                                        <span style="position: absolute;
                                                     top: -20px;
                                                     left: 50%;
                                                     transform: translateX(-50%);
                                                     font-size: 10px;
                                                     font-weight: bold;
                                                     color: #333;
                                                     white-space: nowrap;
                                                     background: white;
                                                     padding: 2px 6px;
                                                     border-radius: 3px;
                                                     box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                                            {{ $marking['label'] }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Haga clic en cualquier parte del cuerpo para agregar una nueva lesión.
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side: Lesions List --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Lesiones Documentadas
                    </h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($existingLesions as $lesion)
                        <div class="lesion-card card mb-2 border-start border-4" style="border-left-color: {{ $lesion['risk_color'] ?? '#ccc' }} !important;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge" style="background-color: {{ $lesion['risk_color'] ?? '#ccc' }}">
                                                {{ ucfirst($lesion['risk_level']) }}
                                            </span>
                                            {{ ucfirst(str_replace('_', ' ', $lesion['lesion_type'])) }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $lesion['body_structure']['full_location'] ?? 'Sin ubicación' }}
                                        </small>
                                        <br>
                                        @if($lesion['size_length_mm'])
                                            <small class="text-muted">
                                                <i class="fas fa-ruler me-1"></i>
                                                {{ $lesion['size_length_mm'] }}mm × {{ $lesion['size_width_mm'] }}mm
                                            </small>
                                        @endif
                                    </div>
                                    <div class="btn-group-vertical btn-group-sm">
                                        <button type="button"
                                                wire:click="editLesion({{ $lesion['id'] }})"
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                                wire:click="deleteLesion({{ $lesion['id'] }})"
                                                onclick="return confirm('¿Está seguro de eliminar esta lesión?')"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No hay lesiones documentadas en esta consulta.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Add/Edit Lesion --}}
    @if($showLesionModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" style="color:#fff;">
                            <i class="fas fa-notes-medical me-2"></i>
                            {{ $editingLesionId ? 'Editar Lesión' : 'Nueva Lesión Cutánea' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showLesionModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveLesion">
                            {{-- Location Information --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-map-marker-alt me-2"></i>Ubicación Anatómica</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Ubicación Anatómica <span class="text-danger">*</span></label>
                                            <select class="form-select @error('location_code') is-invalid @enderror"
                                                    wire:model.live="location_code">
                                                <option value="">Seleccione una ubicación...</option>
                                                @foreach($snomedBodySites as $category => $sites)
                                                    <optgroup label="{{ $category }}">
                                                        @foreach($sites as $site)
                                                            <option value="{{ $site['snomed_code'] }}">
                                                                {{ $site['display_es'] ?? $site['display'] }} ({{ $site['snomed_code'] }})
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @error('location_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @if($location_display)
                                                <small class="text-muted">Ubicación: {{ $location_display }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Lateralidad</label>
                                            <select class="form-select" wire:model="location_qualifier_code">
                                                <option value="">Seleccione...</option>
                                                <option value="7771000">Izquierdo</option>
                                                <option value="24028007">Derecho</option>
                                                <option value="51440002">Bilateral</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Etiqueta</label>
                                            <input type="text" class="form-control" wire:model="marker_label" placeholder="ej: L-001">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Forma del Marcador</label>
                                            <select class="form-select" wire:model="marker_shape">
                                                <option value="circle">● Círculo</option>
                                                <option value="square">■ Cuadrado</option>
                                                <option value="triangle">▲ Triángulo</option>
                                                <option value="star">★ Estrella</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Lesion Characteristics --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-stethoscope me-2"></i>Características de la Lesión</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tipo de Lesión <span class="text-danger">*</span></label>
                                            <select class="form-select @error('lesion_type') is-invalid @enderror" wire:model="lesion_type">
                                                <option value="nevus">Nevus</option>
                                                <option value="melanoma">Melanoma</option>
                                                <option value="basal_cell_carcinoma">Carcinoma Basocelular</option>
                                                <option value="squamous_cell_carcinoma">Carcinoma de Células Escamosas</option>
                                                <option value="keratosis">Queratosis</option>
                                                <option value="wart">Verruga</option>
                                                <option value="cyst">Quiste</option>
                                                <option value="lipoma">Lipoma</option>
                                                <option value="hemangioma">Hemangioma</option>
                                                <option value="other">Otro</option>
                                            </select>
                                            @error('lesion_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Color</label>
                                            <select class="form-select" wire:model="color">
                                                <option value="brown">Marrón</option>
                                                <option value="black">Negro</option>
                                                <option value="red">Rojo</option>
                                                <option value="pink">Rosado</option>
                                                <option value="tan">Canela</option>
                                                <option value="blue">Azul</option>
                                                <option value="purple">Púrpura</option>
                                                <option value="white">Blanco</option>
                                                <option value="mixed">Mixto</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Largo (mm)</label>
                                            <input type="number" step="0.01" class="form-control @error('size_length_mm') is-invalid @enderror"
                                                   wire:model="size_length_mm" placeholder="0.00">
                                            @error('size_length_mm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Ancho (mm)</label>
                                            <input type="number" step="0.01" class="form-control @error('size_width_mm') is-invalid @enderror"
                                                   wire:model="size_width_mm" placeholder="0.00">
                                            @error('size_width_mm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Alto (mm)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                   wire:model="size_height_mm" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ABCDE Criteria --}}
                            <div class="card mb-3">
                                <div class="card-header bg-warning">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Criterios ABCDE (Melanoma Screening)</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><strong>A</strong>simetría</label>
                                            <select class="form-select" wire:model="asymmetry">
                                                <option value="symmetric">Simétrica</option>
                                                <option value="asymmetric">Asimétrica</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><strong>B</strong>orden</label>
                                            <select class="form-select" wire:model="border">
                                                <option value="regular">Regular</option>
                                                <option value="irregular">Irregular</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><strong>C</strong>olor</label>
                                            <input type="text" class="form-select" wire:model="color_variation" placeholder="Uniforme/Variado">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>E</strong>volución</label>
                                            <select class="form-select" wire:model="evolving">
                                                <option value="stable">Estable</option>
                                                <option value="changing">Cambiando</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nivel de Riesgo <span class="text-danger">*</span></label>
                                            <select class="form-select @error('risk_level') is-invalid @enderror" wire:model="risk_level">
                                                <option value="low">Bajo</option>
                                                <option value="moderate">Moderado</option>
                                                <option value="high">Alto</option>
                                                <option value="suspicious">Sospechoso</option>
                                            </select>
                                            @error('risk_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Clinical Assessment --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-clipboard-check me-2"></i>Evaluación Clínica</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Notas Clínicas</label>
                                            <textarea class="form-control" wire:model="clinical_notes" rows="3"
                                                      placeholder="Observaciones adicionales..."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model="requires_biopsy" id="requiresBiopsy">
                                                <label class="form-check-label" for="requiresBiopsy">Requiere Biopsia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model="requires_removal" id="requiresRemoval">
                                                <label class="form-check-label" for="requiresRemoval">Requiere Remoción</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Próximo Seguimiento</label>
                                            <input type="date" class="form-control" wire:model="next_follow_up_date">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Foto</label>
                                            <input type="file" class="form-control" wire:model="photo" accept="image/*">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Plan de Tratamiento</label>
                                            <textarea class="form-control" wire:model="treatment_plan" rows="2"
                                                      placeholder="Plan de tratamiento..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showLesionModal', false)">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveLesion">
                            <i class="fas fa-save me-1"></i> Guardar Lesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- JavaScript for Click Detection and Drag & Drop --}}
    @script
    <script>
        let isDragging = false;
        let dragStartTime = 0;
        let dragStartX = 0;
        let dragStartY = 0;

        function initDermatologyDiagram() {
            const diagram = document.getElementById('bodyDiagram');
            if (!diagram) return;

            // Remove old listeners
            const oldDiagram = diagram.cloneNode(true);
            diagram.parentNode.replaceChild(oldDiagram, diagram);
            const newDiagram = document.getElementById('bodyDiagram');

            // Click on diagram to add new marker
            newDiagram.addEventListener('click', function(e) {
                if (isDragging || e.target.closest('.lesion-marker')) {
                    return;
                }

                const rect = newDiagram.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;

                $wire.call('addMarkerAt', x, y);
            });

            // Drag & drop for markers
            document.querySelectorAll('.lesion-marker').forEach(marker => {
                marker.addEventListener('mousedown', function(e) {
                    if (e.button !== 0) return; // Only left click

                    e.preventDefault();
                    e.stopPropagation();

                    isDragging = false;
                    dragStartTime = Date.now();
                    dragStartX = e.clientX;
                    dragStartY = e.clientY;

                    const lesionId = parseInt(this.dataset.lesionId);
                    const diagram = document.getElementById('bodyDiagram');
                    const diagramRect = diagram.getBoundingClientRect();
                    const markerElement = this;

                    // Visual feedback
                    markerElement.style.transition = 'none';
                    markerElement.style.zIndex = '9999';

                    function onMouseMove(e) {
                        const moveDistance = Math.sqrt(
                            Math.pow(e.clientX - dragStartX, 2) +
                            Math.pow(e.clientY - dragStartY, 2)
                        );

                        // Only start dragging if moved more than 5px
                        if (moveDistance > 5) {
                            isDragging = true;
                            markerElement.style.opacity = '0.7';

                            const x = ((e.clientX - diagramRect.left) / diagramRect.width) * 100;
                            const y = ((e.clientY - diagramRect.top) / diagramRect.height) * 100;

                            const constrainedX = Math.max(5, Math.min(95, x));
                            const constrainedY = Math.max(5, Math.min(95, y));

                            markerElement.style.left = constrainedX + '%';
                            markerElement.style.top = constrainedY + '%';
                        }
                    }

                    function onMouseUp(e) {
                        const dragDuration = Date.now() - dragStartTime;

                        // Reset visual feedback
                        markerElement.style.opacity = '1';
                        markerElement.style.zIndex = '';
                        markerElement.style.transition = 'all 0.2s';

                        if (isDragging) {
                            // Was dragging - update position
                            const x = ((e.clientX - diagramRect.left) / diagramRect.width) * 100;
                            const y = ((e.clientY - diagramRect.top) / diagramRect.height) * 100;

                            const constrainedX = Math.max(5, Math.min(95, x));
                            const constrainedY = Math.max(5, Math.min(95, y));

                            $wire.call('updateMarkerPosition', lesionId, constrainedX, constrainedY);

                            setTimeout(() => { isDragging = false; }, 100);
                        } else {
                            // Was a click - edit lesion
                            $wire.call('editLesion', lesionId);
                        }

                        document.removeEventListener('mousemove', onMouseMove);
                        document.removeEventListener('mouseup', onMouseUp);
                    }

                    document.addEventListener('mousemove', onMouseMove);
                    document.addEventListener('mouseup', onMouseUp);
                });
            });
        }

        // Initialize on component mount
        initDermatologyDiagram();

        // Re-initialize after Livewire updates
        Livewire.hook('morph.updated', ({ component }) => {
            setTimeout(() => initDermatologyDiagram(), 50);
        });
    </script>
    @endscript

    <style>
        .lesion-marker {
            cursor: grab;
            user-select: none;
        }

        .lesion-marker:hover {
            transform: translate(-50%, -50%) scale(1.2);
            z-index: 1000;
        }

        .lesion-marker:active {
            cursor: grabbing;
        }

        .dermatology-section .modal {
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Prevent text selection during drag */
        .body-diagram-container {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
    </style>
</div>
