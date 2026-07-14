<div>
    @if($showModal)
        @teleport('body')
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <h2 class="modal-title" style="margin: 0; font-size: 1.5rem; font-weight: 600;">
                        Historial de Cambios de Estatus
                    </h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #6b7280;">&times;</button>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    @if(count($statusHistory) > 0)
                        <div class="timeline">
                            @foreach($statusHistory as $history)
                                <div class="timeline-item" style="position: relative; padding-left: 40px; padding-bottom: 30px; border-left: 2px solid #e5e7eb; margin-left: 15px;">
                                    <!-- Timeline dot -->
                                    <div style="position: absolute; left: -8px; top: 0; width: 14px; height: 14px; border-radius: 50%; background: #3b82f6; border: 3px solid white;"></div>

                                    <!-- Content -->
                                    <div class="card" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                            <div>
                                                @if($history['previous_status'])
                                                    <span class="badge" style="background-color: #FFD700; color: black; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">
                                                        {{ $history['previous_status_label'] }}
                                                    </span>
                                                    <i class="fas fa-arrow-right" style="margin: 0 8px; color: #6b7280;"></i>
                                                @endif
                                                <span class="badge" style="background-color: #2196F3; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">
                                                    {{ $history['status_label'] }}
                                                </span>
                                            </div>
                                            <small class="text-muted" style="white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($history['created_at'])->format('d/m/Y H:i') }}
                                            </small>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <i class="fas fa-user" style="color: #6b7280; margin-right: 5px;"></i>
                                            <span style="font-size: 0.875rem; color: #374151;">
                                                <strong>Usuario:</strong> {{ $history['user_name'] }}
                                            </span>
                                        </div>

                                        @if($history['observation'])
                                            <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 4px; border-left: 3px solid #3b82f6;">
                                                <i class="fas fa-comment-dots" style="color: #6b7280; margin-right: 5px;"></i>
                                                <span style="font-size: 0.875rem; color: #4b5563;">
                                                    {{ $history['observation'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info" style="padding: 15px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; color: #1e40af;">
                            <i class="fas fa-info-circle"></i>
                            No hay historial de cambios de estatus para esta consulta.
                        </div>
                    @endif
                </div>

                <div class="modal-footer" style="padding: 15px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end;">
                    <button wire:click="closeModal" class="btn btn-secondary" style="padding: 8px 16px;">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>
