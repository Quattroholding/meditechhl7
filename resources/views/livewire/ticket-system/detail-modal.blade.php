<div>
    @if($showModal && $ticket)
    <!-- Modal -->
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop style="max-width: 1000px">
                <div class="modal-header bg-{{$ticket->status->color()}}" >
                    <h2 class="modal-title"> <i class="fas fa-ticket-alt me-2"></i>
                        {{ $ticket->identifier }} - {{ $ticket->title }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <!-- Columna Izquierda: Descripción y Comentarios -->
                        <div class="col-md-8">
                            <!-- Descripción Original -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-file-text me-2"></i>Descripción del Ticket</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            Creado por: <strong>{{ $ticket->user->full_name }}</strong> el {{ $ticket->created_at }}
                                        </small>
                                    </div>
                                    <p class="mb-3">{!! $ticket->description !!}</p>

                                    <!-- Archivos adjuntos -->
                                    @if($ticket->files && count($ticket->files) > 0)
                                    <div class="mt-3">
                                        <h6><i class="fas fa-paperclip me-2"></i>Archivos Adjuntos:</h6>
                                        <div class="list-group">
                                            @foreach($ticket->files as $file)
                                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="list-group-item list-group-item-action">
                                                    <i class="fas fa-download me-2"></i>{{ $file->name }}
                                                    <small class="text-muted">({{ $file->extention }})</small>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Comentarios -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-comments me-2"></i>Comentarios ({{ $ticket->comments->count() }})</h6>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    @forelse($ticket->comments as $comment)
                                        @if(!$comment->is_internal || $canManage)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong>{{ $comment->user->full_name }}</strong>
                                                    @if($comment->is_internal)
                                                        <span class="badge bg-warning text-dark ms-2">
                                                            <i class="fas fa-lock"></i> Nota Interna
                                                        </span>
                                                    @endif
                                                    @if($comment->marks_as_resolved)
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-check"></i> Marcó como Resuelto
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <p class="mb-0">{{ $comment->comment }}</p>
                                        </div>
                                        @endif
                                    @empty
                                        <p class="text-muted text-center py-3">No hay comentarios aún</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Formulario de Nuevo Comentario -->
                            @if($ticket->status !== 'closed')
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-plus me-2"></i>Agregar Comentario</h6>
                                </div>
                                <div class="card-body">
                                    <form wire:submit.prevent="addComment">
                                        <div class="mb-3">
                                            <textarea class="form-control @error('newComment') is-invalid @enderror"
                                                      wire:model="newComment" rows="4"
                                                      placeholder="Escriba su comentario aquí..."></textarea>
                                            @error('newComment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if($canManage)
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="isInternalNote" wire:model="isInternalNote">
                                                    <label class="form-check-label" for="isInternalNote">
                                                        <i class="fas fa-lock me-1"></i> Nota Interna (solo visible para administradores)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="markAsResolved" wire:model="markAsResolved">
                                                    <label class="form-check-label" for="markAsResolved">
                                                        <i class="fas fa-check me-1"></i> Marcar ticket como resuelto
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="addComment">
                                                <i class="fas fa-paper-plane me-1"></i> Enviar Comentario
                                            </span>
                                            <span wire:loading wire:target="addComment">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                Enviando...
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Este ticket está cerrado. No se pueden agregar más comentarios.
                                </div>
                            @endif
                        </div>

                        <!-- Columna Derecha: Información y Gestión -->
                        <div class="col-md-4">
                            <!-- Información Básica -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Estado:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge bg-{{$ticket->status->color()}}">
                                                {{ $ticket->status->label() }}
                                            </span>
                                        </dd>

                                        <dt class="col-sm-5">Prioridad:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge" style="background-color: #{{ App\Models\Ticket::priorityColors()[$ticket->priority] }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </dd>

                                        <dt class="col-sm-5">Categoría:</dt>
                                        <dd class="col-sm-7">{{ $ticket->category_label }}</dd>

                                        <dt class="col-sm-5">Creado:</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($ticket->getRawOriginal('created_at'))->format('d/m/Y H:i') }}</dd>

                                        <dt class="col-sm-5">Actualizado:</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($ticket->getRawOriginal('updated_at'))->format('d/m/Y H:i') }}</dd>

                                        @if($canManage && $ticket->assignedTo)
                                        <dt class="col-sm-5">Asignado a:</dt>
                                        <dd class="col-sm-7">{{ $ticket->assignedTo->full_name }}</dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if($canManage)
                                <!-- Panel de Gestión para Administradores -->
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Gestión</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Cambiar Estado -->
                                        <div class="mb-3">
                                            <label for="newStatus" class="form-label"><strong>Cambiar Estado:</strong></label>
                                            <select class="form-select" id="newStatus" wire:model="newStatus">
                                                <option value="open">Abierto</option>
                                                <option value="in_progress">En Progreso</option>
                                                <option value="on_hold">En Espera</option>
                                                <option value="resolved">Resuelto</option>
                                                <option value="closed">Cerrado</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary mt-2 w-100" wire:click="updateStatus">
                                                <i class="fas fa-sync me-1"></i> Actualizar Estado
                                            </button>
                                        </div>

                                        <!-- Asignar -->
                                        <div class="mb-3">
                                            <label for="assignedTo" class="form-label"><strong>Asignar a:</strong></label>
                                            <select class="form-select" id="assignedTo" wire:model="assignedTo">
                                                <option value="">Sin asignar</option>
                                                @foreach($availableAdmins as $admin)
                                                    <option value="{{ $admin->id }}">{{ $admin->full_name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary mt-2 w-100" wire:click="assignTicket">
                                                <i class="fas fa-user-check me-1"></i> Asignar
                                            </button>
                                        </div>

                                        <!-- Botones de Acción -->
                                        <div class="d-grid gap-2">
                                            @if($ticket->status !== 'closed')
                                                <button class="btn btn-success" wire:click="closeTicket">
                                                    <i class="fas fa-check-circle me-1"></i> Cerrar Ticket
                                                </button>
                                            @endif

                                            @if($ticket->canBeReopened())
                                                <button class="btn btn-warning" wire:click="reopenTicket">
                                                    <i class="fas fa-undo me-1"></i> Reabrir Ticket
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Historial de Estados -->
                                @if(count($statusHistory) > 0)
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Estados</h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <ul class="timeline">
                                            @foreach($statusHistory as $history)
                                                <li class="mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                    <div>
                                                        @if($history->old_status)
                                                            <span class="badge bg-secondary">{{ $history->old_status }}</span>
                                                            <i class="fas fa-arrow-right mx-1"></i>
                                                        @endif
                                                        <span class="badge bg-primary">{{ $history->new_status }}</span>
                                                    </div>
                                                    @if($history->user)
                                                        <small class="text-muted">por {{ $history->user->full_name }}</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            @else
                                <!-- Acciones para Usuarios Normales -->
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-hand-pointer me-2"></i>Acciones</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($ticket->status === 'resolved')
                                            <div class="alert alert-success mb-3">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Este ticket ha sido marcado como resuelto. Si la solución funciona correctamente, puede cerrarlo.
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success" wire:click="closeTicket">
                                                    <i class="fas fa-check-circle me-1"></i> Confirmar y Cerrar
                                                </button>
                                                <button class="btn btn-warning" wire:click="reopenTicket">
                                                    <i class="fas fa-times-circle me-1"></i> La solución no funciona (Reabrir)
                                                </button>
                                            </div>
                                        @elseif($ticket->canBeReopened())
                                            <button class="btn btn-warning w-100" wire:click="reopenTicket">
                                                <i class="fas fa-undo me-1"></i> Reabrir Ticket
                                            </button>
                                        @else
                                            <p class="text-muted">El ticket está siendo procesado por el equipo de soporte.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
