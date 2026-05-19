<div>
    @if ($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10002;">
            <div class="modal-content" wire:click.stop style="max-width: 700px;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('service_request.status_change_title') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    @if($serviceRequest)
                        <!-- Información de la Solicitud -->
                        <div class="alert alert-info mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>{{ __('service_request.patient') }}:</strong><br>
                                    {{ $serviceRequest->patient?->name ?? __('service_request.no_data') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('service_request.code') }}:</strong> {{ $serviceRequest->code }}<br>
                                    <strong>{{ __('service_request.current_status') }}:</strong>
                                    <span class="badge bg-{{ $this->getStatusColor($serviceRequest->status) }} ms-1">
                                        {{ __('service_request.status_' . $serviceRequest->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de cambio de estado -->
                        <form wire:submit.prevent="changeStatus">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="newStatus" class="form-label">{{ __('service_request.new_status') }} <span class="text-danger">*</span></label>
                                        <select wire:model="newStatus" id="newStatus" class="form-select @error('newStatus') is-invalid @enderror">
                                            <option value="">{{ __('generic.select') }}</option>
                                            @foreach($availableStatuses as $status)
                                                <option value="{{ $status }}">
                                                    {{ __('service_request.status_' . $status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('newStatus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if($newStatus)
                                            <small class="form-text text-muted mt-1">
                                                {{ __('service_request.status_' . $newStatus . '_description') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="statusReason" class="form-label">{{ __('service_request.status_reason') }}</label>
                                        <textarea
                                            wire:model="statusReason"
                                            id="statusReason"
                                            class="form-control @error('statusReason') is-invalid @enderror"
                                            rows="3"
                                            placeholder="{{ __('service_request.status_reason_placeholder') }}"
                                        ></textarea>
                                        @error('statusReason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">
                                    {{ __('generic.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary" {{ empty($availableStatuses) ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="changeStatus">
                                        <i class="fas fa-exchange-alt me-1"></i>
                                        {{ __('service_request.change_status') }}
                                    </span>
                                    <span wire:loading wire:target="changeStatus">
                                        <i class="fas fa-spinner fa-spin me-1"></i>
                                        {{ __('generic.loading') }}...
                                    </span>
                                </button>
                            </div>
                        </form>

                        @if(empty($availableStatuses))
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay transiciones de estado disponibles desde el estado actual.
                            </div>
                        @endif

                        <!-- Historial de Estados -->
                        @if(count($statusHistory) > 0)
                            <hr>
                            <h6>{{ __('service_request.status_history') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('generic.date') }}</th>
                                            <th>{{ __('generic.before') }}</th>
                                            <th>{{ __('generic.after') }}</th>
                                            <th>{{ __('generic.reason') }}</th>
                                            <th>{{ __('generic.user') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statusHistory as $log)
                                            <tr>
                                                <td>
                                                    <small>{{ $log->changed_at ? $log->changed_at->format('d/m/Y H:i') : $log->created_at }}</small>
                                                </td>
                                                <td>
                                                    @if($log->old_status)
                                                        <span class="badge bg-{{ $this->getStatusColor($log->old_status) }}">
                                                           {{ $log->old_status }}
                                                        </span>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $this->getStatusColor($log->new_status) }}">
                                                        {{  $log->new_status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $log->observation ?: '-' }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $log->user?->full_name ?: __('generic.system') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrStatusManager', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
