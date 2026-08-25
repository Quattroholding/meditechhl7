<div>
    <!-- Modal de Asignación Manual de Slot -->
    @if($showModal && $freedSlot)
        @teleport('body')
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10002; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 12px; padding: 0;">

                <!-- Header -->
                <div style="border-bottom: 1px solid #e9ecef; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <h5 style="margin: 0; color: #bf360c;">
                        <i class="fas fa-hand-holding me-2"></i>Asignar Espacio Disponible
                    </h5>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">
                        &times;
                    </button>
                </div>

                <!-- Body -->
                <div style="padding: 20px;">
                    <!-- Información del Slot Disponible -->
                    <div style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                        <h6 style="margin: 0 0 10px 0; color: #e65100; font-weight: 600;">
                            <i class="fas fa-calendar-check me-2"></i>Espacio Disponible
                        </h6>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; color: #555;">
                            <div>
                                <small style="color: #999;">📅 Fecha</small><br>
                                <strong>{{ \Carbon\Carbon::parse($freedSlot->slot_date)->format('d/m/Y') }}</strong>
                            </div>
                            <div>
                                <small style="color: #999;">🕐 Hora</small><br>
                                <strong>{{ \Carbon\Carbon::createFromFormat('H:i:s', $freedSlot->slot_start_time)->format('H:i') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $freedSlot->slot_end_time)->format('H:i') }}</strong>
                            </div>
                            <div>
                                <small style="color: #999;">⏱️ Duración</small><br>
                                <strong>{{ $freedSlot->duration_minutes }} min</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Candidatos Sugeridos -->
                    <div style="margin-bottom: 20px;">
                        <h6 style="color: #bf360c; margin-bottom: 15px; font-weight: 600;">
                            <i class="fas fa-star me-2"></i>Candidatos Sugeridos por Score de Coincidencia
                        </h6>

                        @if(count($suggestedCandidates) > 0)
                            <div style="border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                                @foreach($suggestedCandidates as $candidate)
                                    <div wire:click="$set('selectedEntryId', {{ $candidate['entry_id'] }})"
                                         style="padding: 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.3s ease; background: {{ $selectedEntryId === $candidate['entry_id'] ? '#fff3e0' : '#fff' }}; display: flex; justify-content: space-between; align-items: center;">

                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                                <strong style="font-size: 1.1rem; color: #333;">{{ $candidate['patient_name'] }}</strong>
                                                <span style="background: #e65100; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                                    {{ $candidate['urgency'] }}
                                                </span>
                                            </div>

                                            <div style="display: grid; grid-template-columns: auto auto auto; gap: 20px; color: #666; font-size: 0.9rem;">
                                                <div>
                                                    <small style="color: #999;">Esperando</small><br>
                                                    <strong>{{ $candidate['days_waiting'] }} días</strong>
                                                </div>
                                                <div>
                                                    <small style="color: #999;">Prioridad</small><br>
                                                    <strong>{{ number_format($candidate['priority_score'], 1) }}/100</strong>
                                                </div>
                                                <div>
                                                    <small style="color: #999;">Match</small><br>
                                                    <strong>{{ number_format($candidate['match_score'], 1) }}/100</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="margin-left: 15px;">
                                            @if($selectedEntryId === $candidate['entry_id'])
                                                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #ff9800;"></i>
                                            @else
                                                <i class="fas fa-circle" style="font-size: 1.5rem; color: #ccc;"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="background: #f8f9fa; padding: 30px; text-align: center; border-radius: 8px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                <p>No hay candidatos disponibles en la lista de espera para este espacio.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Información de Asignación -->
                    @if($selectedEntryId)
                        <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                            <h6 style="margin: 0 0 10px 0; color: #2e7d32; font-weight: 600;">
                                <i class="fas fa-check me-2"></i>Paciente Seleccionado
                            </h6>
                            <p style="margin: 0; color: #555;">
                                Este paciente será notificado automáticamente cuando se confirme la asignación.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div style="border-top: 1px solid #e9ecef; padding: 15px; display: flex; gap: 10px; justify-content: flex-end; background: #f8f9fa;">
                    <button wire:click="closeModal" class="btn btn-secondary" type="button">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="assignSlot"
                        @if(!$selectedEntryId) disabled @endif
                        style="background: {{ $selectedEntryId ? '#ff9800' : '#ccc' }}; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: {{ $selectedEntryId ? 'pointer' : 'not-allowed' }}; font-weight: 600; transition: all 0.3s ease;"
                        @if($selectedEntryId)
                            onmouseover="this.style.background='#e68900';"
                            onmouseout="this.style.background='#ff9800';"
                        @endif>
                        <i class="fas fa-check me-2"></i>Confirmar Asignación
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <script wire:ignore>
        function showManualSlotToastr(event) {
            if (event && event.type && event.message) {
                if (typeof toastr !== 'undefined' && toastr[event.type]) {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                } else {
                    // Si toastr no está disponible, reintentar en 100ms
                    setTimeout(() => showManualSlotToastr(event), 100);
                }
            }
        }

        document.addEventListener('livewire:initialized', () => {
            // Escuchar evento global-show-manual-assignment desde Calendar
            Livewire.on('global-show-manual-assignment', ({ slotId }) => {
                console.log('ManualSlotAssignment received global-show-manual-assignment event', { slotId });
                @this.show(slotId);
            });

            // Reemitir appointment-assigned globalmente para que Calendar lo reciba
            Livewire.on('appointment-assigned', () => {
                console.log('ManualSlotAssignment: appointment-assigned event, reemitting globally');
                Livewire.dispatch('global-appointment-assigned');
            });

            Livewire.on('showToastrManualSlotassigment', (event) => {
                showManualSlotToastr(event);
            });
        });
    </script>
</div>
