<div>
    @include('partials.message')

    {{-- Security Information --}}
    <div class="alert alert-info border-0">
        <div class="d-flex align-items-start">
            <i class="fas fa-shield-alt fa-2x me-3 text-primary"></i>
            <div>
                <h6 class="alert-heading mb-2">
                    <strong>Protege tu información y la de tus pacientes</strong>
                </h6>
                <p class="mb-2">
                    La autenticación de dos factores (2FA) añade una capa adicional de seguridad a tu cuenta.
                    Incluso si alguien obtiene tu contraseña, no podrá acceder sin el código de tu teléfono.
                </p>
                <p class="mb-0 small">
                    <i class="fas fa-check-circle text-success me-1"></i> Protege datos médicos sensibles<br>
                    <i class="fas fa-check-circle text-success me-1"></i> Cumple con estándares de seguridad<br>
                    <i class="fas fa-check-circle text-success me-1"></i> Previene accesos no autorizados
                </p>
            </div>
        </div>
    </div>

    {{-- Not enabled state --}}
    @if(!$enabled && !$showingQrCode)
        <div class="alert alert-warning border-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @if($required)
                <strong>Por tu seguridad y la de tus pacientes, tu rol requiere autenticación de dos factores.</strong>
                Actívala ahora para proteger el acceso a información médica sensible.
            @else
                <strong>Recomendamos activar 2FA para proteger tu cuenta.</strong>
                Es una medida simple que aumenta significativamente la seguridad de tus datos.
            @endif
        </div>

        <form wire:submit="enableTwoFactor">
            @csrf
            <div class="mb-3">
                <label class="form-label">Contraseña Actual</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       wire:model="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="fas fa-shield-alt me-1"></i> Activar 2FA
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1"></span> Procesando...
                </span>
            </button>
        </form>
    @endif

    {{-- QR Code setup --}}
    @if($showingQrCode && $confirming)
        <div class="alert alert-info">
            <strong>Paso 1:</strong> Escanea este código QR con tu aplicación de autenticación (Microsoft Authenticator, Google Authenticator, Authy, etc.)
        </div>

        <div class="text-center my-4">
            {!! $qrCode !!}
        </div>

        <div class="alert alert-info">
            <strong>Paso 2:</strong> Ingresa el código de 6 dígitos de tu aplicación para confirmar.
        </div>

        <form wire:submit="confirmTwoFactor">
            @csrf
            <div class="mb-3">
                <label class="form-label">Código de Verificación</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror"
                       wire:model="code" maxlength="6" pattern="[0-9]{6}"
                       placeholder="000000" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-check me-1"></i> Confirmar
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span> Verificando...
                    </span>
                </button>
                <button type="button" class="btn btn-danger" wire:click="cancelSetup">
                    <i class="fas fa-close me-1"></i> Cancelar
                </button>
            </div>
        </form>
    @endif

    {{-- Recovery codes display --}}
    @if($showingRecoveryCodes)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡IMPORTANTE!</strong> Guarda estos códigos de recuperación en un lugar seguro.
            Podrás usarlos si pierdes acceso a tu aplicación de autenticación.
        </div>

        <div class="card bg-light">
            <div class="card-body">
                <div class="row">
                    @foreach($recoveryCodes as $code)
                        <div class="col-md-6">
                            <code class="d-block p-2">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-3" x-data="{
            codes: @js($recoveryCodes->toArray()),
            downloadCodes() {
                const codesText = this.codes.join('\n');
                const blob = new Blob([codesText], {type: 'text/plain'});
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'sami-recovery-codes.txt';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                }, 100);
            }
        }">
            <button type="button" class="btn btn-primary" @click="downloadCodes()">
                <i class="fas fa-download me-1"></i> Descargar Códigos
            </button>

            <button class="btn btn-secondary" wire:click="$set('showingRecoveryCodes', false)">
                Ya los guardé
            </button>
        </div>
    @endif

    {{-- Enabled state controls --}}
    @if($enabled && !$confirming && !$showingRecoveryCodes)
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            La autenticación de dos factores está activa en tu cuenta.
        </div>

        <div class="row g-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Regenerar Códigos de Recuperación</h6>
                        <p class="card-text small text-muted">
                            Obtén nuevos códigos de recuperación. Los anteriores dejarán de funcionar.
                        </p>

                        <form wire:submit="regenerateRecoveryCodes">
                            @csrf
                            <div class="mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password" placeholder="Contraseña actual" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-warning btn-sm" wire:loading.attr="disabled">
                                <i class="fas fa-sync-alt me-1"></i> Regenerar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if(!$required)
            <div class="col-md-12">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Desactivar 2FA</h6>
                        <p class="card-text small text-muted">
                            Tu cuenta estará menos protegida.
                        </p>

                        <form wire:submit="disableTwoFactor">
                            @csrf
                            <div class="mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password" placeholder="Contraseña actual" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm"
                                    wire:loading.attr="disabled"
                                    onclick="return confirm('¿Estás seguro de desactivar 2FA?')">
                                <i class="fas fa-times me-1"></i> Desactivar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
