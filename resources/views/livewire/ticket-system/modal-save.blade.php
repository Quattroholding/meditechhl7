<div>
    @if($showModal)
    <!-- Modal -->
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title"> <i class="fas fa-ticket-alt me-2"></i>
                        {{ $ticketId ? 'Editar Ticket' : 'Nuevo Ticket' }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Alert informativo -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Complete el formulario con los detalles de su problema o solicitud. Puede adjuntar capturas de pantalla o documentos relevantes.
                    </div>

                    <form wire:submit.prevent="saveTicket">
                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" wire:model="title" placeholder="Resumen breve del problema">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Prioridad -->
                            <div class="col-md-4 mb-3">
                                <label for="priority" class="form-label">Prioridad <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority" wire:model="priority">
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                    <option value="critical">Crítica</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-12 mb-3">
                                <label for="category" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror"
                                        id="category" wire:model="category">
                                    <option value="bug">Error / Bug</option>
                                    <option value="question">Pregunta</option>
                                    <option value="feature_request">Solicitud de Nueva Función</option>
                                    <option value="improvement">Mejora</option>
                                    <option value="technical_support">Soporte Técnico</option>
                                    <option value="other">Otro</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" wire:model="description" rows="6"
                                          placeholder="Describa el problema o solicitud en detalle. Incluya pasos para reproducir el error si aplica."></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Archivos adjuntos -->
                            <div class="col-md-12 mb-3">
                                <label for="attachments" class="form-label">Archivos Adjuntos</label>
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                       id="attachments" wire:model="attachments" multiple
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <small class="form-text text-muted">
                                    Formatos permitidos: JPG, PNG, PDF, DOC, DOCX. Tamaño máximo: 10MB por archivo.
                                </small>
                                @error('attachments.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <!-- Loading indicator para upload -->
                                <div wire:loading wire:target="attachments" class="mt-2">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <span class="ms-2">Cargando archivos...</span>
                                </div>
                            </div>

                            <!-- Lista de archivos seleccionados -->
                            @if(!empty($attachments))
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Archivos Seleccionados:</label>
                                <ul class="list-group">
                                    @foreach($attachments as $index => $attachment)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file me-2"></i>
                                                {{ $attachment->getClientOriginalName() }}
                                                <small class="text-muted">({{ number_format($attachment->getSize() / 1024, 2) }} KB)</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeAttachment({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <!-- Archivos existentes (si es edición) -->
                            @if($ticketId && !empty($existingAttachments) && count($existingAttachments) > 0)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Archivos Existentes:</label>
                                <ul class="list-group">
                                    @foreach($existingAttachments as $file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-paperclip me-2"></i>
                                                {{ $file->name }}
                                            </div>
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveTicket" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveTicket">
                            <i class="fas fa-save me-1"></i> {{ $ticketId ? 'Actualizar' : 'Crear' }} Ticket
                        </span>
                        <span wire:loading wire:target="saveTicket">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
