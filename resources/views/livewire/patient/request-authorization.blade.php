<div>
    @if($this->hasAdditionalEncounters && !$isAuthorized)
        <!-- Botón Sutil -->
        <button
            wire:click="openModal"
            class="btn btn-sm btn-warning d-flex align-items-center gap-2"
            style="border-radius: 20px; padding: 8px 16px;"
        >
            <i class="fas fa-lock"></i>
            <span>Solicitar Acceso Completo al Historial</span>
        </button>
    @endif
    {{--}}
    @if($isAuthorized)
        <div class="alert alert-success d-flex align-items-center mb-0" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <div>
                <strong>Acceso Autorizado</strong>
                <small class="d-block">Tiene acceso completo al historial clínico de este paciente.</small>
            </div>
        </div>
    @endif
    {{--}}

    <!-- Modal de Autorización -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                    <!-- Header -->
                    <div class="modal-header border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white; border-radius: 20px 20px 0 0; padding: 24px;">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-lock fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="modal-title mb-1 fw-bold">Acceso Restringido</h5>
                                <small class="opacity-90">Historial Clínico Completo</small>
                            </div>
                            <button type="button" wire:click="closeModal" class="btn-close btn-close-white" aria-label="Close"></button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                {!! session('message') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(!$showCodeInput)
                            <!-- Información Inicial -->
                            <div class="text-center py-3">
                                <div class="mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                        <i class="fas fa-user-shield fs-1 text-warning"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">Necesita Autorización del Paciente</h6>
                                    <p class="text-muted mb-0">
                                        Este paciente tiene historial médico con otros profesionales.
                                        Para acceder a su historial completo, necesita su autorización.
                                    </p>
                                </div>

                                <div class="alert alert-info d-flex align-items-start gap-2 text-start mb-4">
                                    <i class="fas fa-info-circle mt-1"></i>
                                    <div class="small">
                                        Se enviará un <strong>código de 4 dígitos</strong> al correo del paciente:
                                        <br><strong class="text-primary">{{ $patient->email }}</strong>
                                        <br>El código será válido por <strong>1 hora</strong>.
                                    </div>
                                </div>

                                <button wire:click="requestCode" class="btn btn-warning btn-lg w-100" wire:loading.attr="disabled" style="border-radius: 12px;">
                                    <span wire:loading.remove>
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Código de Autorización
                                    </span>
                                    <span wire:loading>
                                        <i class="fas fa-spinner fa-spin me-2"></i>Enviando código...
                                    </span>
                                </button>
                            </div>
                        @else
                            <!-- Input de Código -->
                            <div class="py-3">
                                <div class="text-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                        <i class="fas fa-envelope-open fs-3 text-success"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">Código Enviado</h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-clock me-1"></i>
                                        El paciente debe proporcionarle el código recibido por correo
                                    </p>
                                </div>

                                <form wire:submit.prevent="verifyCode">
                                    <label class="form-label text-center w-100 mb-3 fw-semibold">
                                        Ingrese el código de 4 dígitos
                                    </label>

                                    <!-- 4 Inputs Individuales -->
                                    <div class="d-flex justify-content-center gap-3 mb-3">
                                        @foreach(['digit1', 'digit2', 'digit3', 'digit4'] as $index => $digit)
                                            <input
                                                type="text"
                                                id="{{ $digit }}"
                                                wire:model.live="{{ $digit }}"
                                                class="form-control text-center fw-bold @error($digit) is-invalid @enderror"
                                                style="width: 60px; height: 60px; font-size: 24px; border-radius: 12px; border: 2px solid #e5e7eb;"
                                                maxlength="1"
                                                pattern="[0-9]"
                                                inputmode="numeric"
                                                data-index="{{ $index }}"
                                                autocomplete="off"
                                            >
                                        @endforeach
                                    </div>

                                    @error('digit1')
                                        <div class="text-danger text-center small mb-3">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-success flex-grow-1" wire:loading.attr="disabled" style="border-radius: 12px;">
                                            <span wire:loading.remove>
                                                <i class="fas fa-check me-1"></i>Verificar
                                            </span>
                                            <span wire:loading>
                                                <i class="fas fa-spinner fa-spin me-1"></i>Verificando...
                                            </span>
                                        </button>
                                        <button type="button" wire:click="requestCode" class="btn btn-outline-secondary" wire:loading.attr="disabled" style="border-radius: 12px;">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function () {
            // Auto-focus y navegación automática entre inputs
            const inputs = ['digit1', 'digit2', 'digit3', 'digit4'];

            inputs.forEach((inputId, index) => {
                const input = document.getElementById(inputId);
                if (!input) return;

                // Auto-focus en el primer input cuando se muestra
                if (index === 0) {
                    setTimeout(() => input.focus(), 300);
                }

                // Manejar entrada
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    // Solo permitir números
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    // Si hay un valor, mover al siguiente input
                    if (value.length === 1 && index < inputs.length - 1) {
                        document.getElementById(inputs[index + 1]).focus();
                    }
                });

                // Manejar tecla de retroceso
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        document.getElementById(inputs[index - 1]).focus();
                    }
                });

                // Manejar paste
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');

                    if (pastedData.length === 4) {
                        inputs.forEach((id, i) => {
                            const targetInput = document.getElementById(id);
                            if (targetInput && pastedData[i]) {
                                targetInput.value = pastedData[i];
                                targetInput.dispatchEvent(new Event('input'));
                            }
                        });
                        document.getElementById('digit4').focus();
                    }
                });
            });
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrPatientRequestAutorization', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                    onHidden: function() {
                        window.location.reload();
                    }
                });
            });

        });
    </script>
    @endpush
</div>
