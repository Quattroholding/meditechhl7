<div>
    @if ($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 600px;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('service_request_result.upload_title') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="save">
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

                        <div class="row">
                            <!-- Tipo de Resultado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="result_type" class="form-label">{{ __('service_request_result.result_type') }} <span class="text-danger">*</span></label>
                                    <select wire:model="result_type" id="result_type" class="form-select @error('result_type') is-invalid @enderror">
                                        <option value="">{{ __('generic.select') }}</option>
                                        <option value="laboratory">{{ __('service_request_result.laboratory') }}</option>
                                        <option value="images">{{ __('service_request_result.imaging') }}</option>
                                        <option value="pathology">{{ __('service_request_result.pathology') }}</option>
                                        <option value="report">{{ __('service_request_result.report') }}</option>
                                        <option value="other">{{ __('service_request_result.other') }}</option>
                                    </select>
                                    @error('result_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ __('service_request_result.status') }} <span class="text-danger">*</span></label>
                                    <select wire:model="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="final">{{ __('service_request_result.status_final') }}</option>
                                        <option value="preliminary">{{ __('service_request_result.status_preliminary') }}</option>
                                        <option value="corrected">{{ __('service_request_result.status_corrected') }}</option>
                                        <option value="amended">{{ __('service_request_result.status_amended') }}</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{--}}
                        <div class="row">
                            <!-- Código -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">{{ __('service_request_result.code') }}</label>
                                    <input type="text" wire:model="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="{{ __('service_request_result.code_placeholder') }}">
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Descripción del Código -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code_display" class="form-label">{{ __('service_request_result.code_display') }}</label>
                                    <input type="text" wire:model="code_display" id="code_display" class="form-control @error('code_display') is-invalid @enderror" placeholder="{{ __('service_request_result.code_display_placeholder') }}">
                                    @error('code_display')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{--}}

                        <!-- Fecha del Resultado -->
                        <div class="mb-3">
                            <label for="result_date" class="form-label">{{ __('service_request_result.result_date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" wire:model="result_date" id="result_date" class="form-control @error('result_date') is-invalid @enderror">
                            @error('result_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Archivo -->
                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('service_request_result.file') }} <span class="text-danger">*</span></label>
                            <input type="file" wire:model="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            <small class="form-text text-muted">
                                {{ __('service_request_result.file_help') }} ({{ __('service_request_result.max_size') }}: 10MB)
                            </small>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($file)
                                <div class="mt-2">
                                    <div class="alert alert-success d-flex align-items-center">
                                        <i class="fas fa-file me-2"></i>
                                        <span>{{ $file->getClientOriginalName() }} ({{ number_format($file->getSize()/1024, 2) }} KB)</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <!-- Interpretación -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interpretation" class="form-label">{{ __('service_request_result.interpretation') }}</label>
                                    <select wire:model="interpretation" id="interpretation" class="form-select @error('interpretation') is-invalid @enderror">
                                        <option value="">{{ __('generic.select') }}</option>
                                        <option value="normal">{{ __('service_request_result.interpretation_normal') }}</option>
                                        <option value="abnormal">{{ __('service_request_result.interpretation_abnormal') }}</option>
                                        <option value="high">{{ __('service_request_result.interpretation_high') }}</option>
                                        <option value="low">{{ __('service_request_result.interpretation_low') }}</option>
                                        <option value="critical">{{ __('service_request_result.interpretation_critical') }}</option>
                                    </select>
                                    @error('interpretation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Rango de Referencia -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_range" class="form-label">{{ __('service_request_result.reference_range') }}</label>
                                    <input type="text" wire:model="reference_range" id="reference_range" class="form-control @error('reference_range') is-invalid @enderror" placeholder="{{ __('service_request_result.reference_range_placeholder') }}">
                                    @error('reference_range')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="observations" class="form-label">{{ __('service_request_result.observations') }}</label>
                            <textarea wire:model="observations" id="observations" class="form-control @error('observations') is-invalid @enderror" rows="3" placeholder="{{ __('service_request_result.observations_placeholder') }}"></textarea>
                            @error('observations')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('service_request_result.notes') }}</label>
                            <textarea wire:model="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="{{ __('service_request_result.notes_placeholder') }}"></textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('generic.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fas fa-upload me-1"></i>
                                    {{ __('service_request_result.upload') }}
                                </span>
                            <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                    {{ __('generic.loading') }}...
                                </span>
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-dialog -->
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
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
