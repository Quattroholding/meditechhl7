<div>
    @if ($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10001;">
            <div class="modal-content" wire:click.stop style="max-width: 900px;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('service_request_result.results_title') }}</h5>
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
                                    <strong>{{ __('service_request.description') }}:</strong><br>
                                    <small>{{ $serviceRequest->cpt?->description_es ?? $serviceRequest->code_display }}</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedResult)
                        <!-- Vista detallada de un resultado -->
                        <div class="result-detail">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">{{ __('service_request_result.result_details') }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="closeResultDetail">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    {{ __('generic.back') }}
                                </button>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ __('service_request_result.result_type') }}</label>
                                                <div>
                                                    <span class="badge bg-primary">
                                                        {{ __('service_request_result.' . $selectedResult->result_type) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ __('service_request_result.status') }}</label>
                                                <div>
                                                    @php
                                                        $statusColor = match($selectedResult->status) {
                                                            'final' => 'success',
                                                            'preliminary' => 'warning',
                                                            'corrected' => 'info',
                                                            'amended' => 'secondary',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">
                                                        {{ __('service_request_result.status_' . $selectedResult->status) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ __('service_request_result.result_date') }}</label>
                                                <div>{{ $selectedResult->result_date ? \Carbon\Carbon::parse($selectedResult->result_date)->format('d/m/Y H:i') : __('service_request.no_data') }}</div>
                                            </div>

                                            @if($selectedResult->code)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.code') }}</label>
                                                    <div>{{ $selectedResult->code }}</div>
                                                </div>
                                            @endif

                                            @if($selectedResult->code_display)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.code_display') }}</label>
                                                    <div>{{ $selectedResult->code_display }}</div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            @if($selectedResult->interpretation)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.interpretation') }}</label>
                                                    <div>
                                                        @php
                                                            $interpretationColor = match($selectedResult->interpretation) {
                                                                'normal' => 'success',
                                                                'abnormal' => 'warning',
                                                                'high' => 'danger',
                                                                'low' => 'info',
                                                                'critical' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $interpretationColor }}">
                                                            {{ __('service_request_result.interpretation_' . $selectedResult->interpretation) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($selectedResult->reference_range)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.reference_range') }}</label>
                                                    <div>{{ $selectedResult->reference_range }}</div>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">{{ __('service_request_result.uploaded_at') }}</label>
                                                <div>{{ $selectedResult->uploaded_at ? $selectedResult->uploaded_at->format('d/m/Y H:i') : __('service_request.no_data') }}</div>
                                            </div>

                                            @if($selectedResult->practitioner)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.uploaded_by') }}</label>
                                                    <div>{{ $selectedResult->practitioner->name }}</div>
                                                </div>
                                            @endif

                                            @if($selectedResult->file_name)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('service_request_result.file_name') }}</label>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file me-2"></i>
                                                        <span>{{ $selectedResult->file_name }}</span>
                                                        @if($selectedResult->file_size)
                                                            <small class="text-muted ms-2">({{ number_format($selectedResult->file_size/1024, 2) }} KB)</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($selectedResult->observations)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('service_request_result.observations') }}</label>
                                            <div class="border rounded p-2 bg-light">{{ $selectedResult->observations }}</div>
                                        </div>
                                    @endif

                                    @if($selectedResult->notes)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('service_request_result.notes') }}</label>
                                            <div class="border rounded p-2 bg-light">{{ $selectedResult->notes }}</div>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 mt-3">
                                        @if($selectedResult->file_path)
                                            <button type="button" class="btn btn-success btn-sm" wire:click="downloadFile({{ $selectedResult->id }})">
                                                <i class="fas fa-download me-1"></i>
                                                {{ __('service_request_result.download') }}
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            wire:click="deleteResult({{ $selectedResult->id }})"
                                            wire:confirm="{{ __('service_request_result.delete_confirmation') }}"
                                        >
                                            <i class="fas fa-trash me-1"></i>
                                            {{ __('service_request_result.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Lista de resultados -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                {{ trans_choice('service_request_result.results_count', count($results), ['count' => count($results)]) }}
                            </h6>
                            <button type="button" class="btn btn-primary btn-sm" wire:click="openUploadModal">
                                <i class="fas fa-upload me-1"></i>
                                {{ __('service_request_result.upload_new') }}
                            </button>
                        </div>

                        @if(count($results) > 0)
                            <div class="results-list">
                                @foreach($results as $result)
                                    <div class="card mb-2">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file me-2 text-primary"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $result->file_name }}</div>
                                                            <small class="text-muted">{{ __('service_request_result.' . $result->result_type) }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    @php
                                                        $statusColor = match($result->status) {
                                                            'final' => 'success',
                                                            'preliminary' => 'warning',
                                                            'corrected' => 'info',
                                                            'amended' => 'secondary',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">
                                                        {{ __('service_request_result.status_' . $result->status) }}
                                                    </span>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted">
                                                        {{ $result->result_date ? \Carbon\Carbon::parse($result->result_date)->format('d/m/Y') : __('service_request.no_data') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">
                                                        {{ $result->practitioner?->name ?? __('service_request.no_data') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="viewResult({{ $result->id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($result->file_path)
                                                            <button type="button" class="btn btn-outline-success btn-sm" wire:click="downloadFile({{ $result->id }})">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-file-medical-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">{{ __('service_request_result.no_results') }}</h6>
                                <p class="text-muted">{{ __('service_request_result.no_results_description') }}</p>
                                <button type="button" class="btn btn-primary" wire:click="openUploadModal">
                                    <i class="fas fa-upload me-1"></i>
                                    {{ __('service_request_result.upload_first') }}
                                </button>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('generic.close') }}</button>
                </div>
            </div>
        </div>
    @endif
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('showToastrSrViewResult', (event) => {
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
