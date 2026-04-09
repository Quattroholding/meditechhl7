<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Actualizar Plan de Suscripción
                @endslot
                @slot('li_1')
                    <a href="{{ route('suscriptions.show') }}">Mi Suscripción</a>
                @endslot
                @slot('li_2')
                    Actualizar Plan
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Current Plan Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Plan Actual: {{ $subscription->package->name }}</strong><br>
                            <small>
                                Precio: ${{ number_format($subscription->package->base_price, 2) }} / {{ ucfirst($subscription->package->billing_period->value) }}
                                @if(auth()->user()->hasReachedAppointmentsLimit())
                                    <span class="badge bg-warning ms-2">Límite de citas alcanzado</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            @if($packages->isEmpty())
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay planes disponibles</h5>
                                <p class="text-muted">No hay planes superiores disponibles en este momento.</p>
                                <a href="{{ route('suscriptions.show') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Mi Suscripción
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Available Plans -->
                <div class="row">

                    @foreach($packages as $package)
                        @php
                            $isUpgrade = $package->base_price > $subscription->package->base_price;
                            $isDowngrade = $package->base_price < $subscription->package->base_price;
                            $priceDifference = $package->base_price - $subscription->package->base_price;
                        @endphp

                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 {{ $isUpgrade ? 'border-primary' : '' }}">
                                @if($isUpgrade && $package->base_price >= $subscription->package->base_price * 1.5)
                                    <div class="card-header bg-primary text-white text-center">
                                        <i class="fas fa-star me-1"></i>Popular
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <!-- Plan Name -->
                                    <h4 class="card-title text-center">{{ $package->name }}</h4>
                                    @if($package->id <>4)
                                    <!-- Price -->
                                    <div class="text-center mb-3">
                                        <h2 class="text-primary mb-0">${{ number_format($package->base_price, 2) }}</h2>
                                        <small class="text-muted">/ {{ ucfirst($package->billing_period->value) }}</small>

                                        @if($priceDifference != 0)
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $priceDifference > 0 ? 'success' : 'danger' }}">
                                                    {{ $priceDifference > 0 ? '+' : '' }}${{ number_format(abs($priceDifference), 2) }} / mes
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Description -->
                                    <p class="text-muted text-center mb-3">{{ $package->description }}</p>

                                    <!-- Key Info -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted"><i class="fas fa-users me-2"></i>Usuarios</span>
                                            <strong>{{ $package->max_users }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted"><i class="fas fa-user-md me-2"></i>Médicos</span>
                                            <strong>{{ $package->max_doctors_included }}</strong>
                                        </div>
                                        @if($package->appointments_limit)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>Citas/mes</span>
                                                <strong>{{ $package->appointments_limit }}</strong>
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted"><i class="fas fa-calendar-check me-2"></i>Citas/mes</span>
                                                <strong class="text-success">Ilimitadas</strong>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted"><i class="fas fa-robot me-2"></i>Agente SAMI</span>
                                            <strong>
                                                @if($package->agent_available)
                                                    <span class="text-success"><i class="fas fa-check-circle"></i> Sí</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-times-circle"></i> No</span>
                                                @endif
                                            </strong>
                                        </div>
                                    </div>

                                    @endif

                                    <!-- Features -->
                                    @if($package->features && count($package->features) > 0)
                                        <div class="mb-3">
                                            <h6 class="mb-2">Características:</h6>
                                            <ul class="list-unstyled">
                                                @foreach(array_slice($package->features, 0, 5) as $feature)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check text-success me-2"></i>
                                                        <small>{{ $feature }}</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Action Button -->
                                    <div class="mt-auto">

                                        @if($isUpgrade)
                                            @if($package->id == 4)

                                                {{-- Plan Empresarial - Contactar Ventas --}}
                                                <button type="button" class="btn btn-primary w-100" onclick="openEnterpriseModal()">
                                                    <i class="fas fa-phone me-2"></i>Contactar Ventas
                                                </button>
                                            @else
                                                {{-- Planes regulares - Upgrade normal --}}
                                                <form action="{{ route('suscriptions.upgrade.process') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                    <input type="hidden" name="prorate" value="1">
                                                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('¿Está seguro que desea actualizar a este plan? Se calculará el prorrateo correspondiente.')">
                                                        <i class="fas fa-arrow-up me-2"></i>Actualizar a este Plan
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif($isDowngrade)
                                            <button type="button" class="btn btn-secondary w-100" disabled>
                                                <i class="fas fa-info-circle me-2"></i>Plan de Menor Precio
                                            </button>
                                            <small class="text-muted d-block mt-2 text-center">Contacte con soporte para cambiar a un plan inferior</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Information Card -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Importante</h5>
                                <ul class="mb-0">
                                    <li>Al actualizar su plan, se calculará automáticamente el <strong>prorrateo</strong> correspondiente al tiempo restante de su período actual.</li>
                                    <li>El cambio de plan es <strong>inmediato</strong>.</li>
                                    <li>Se generará un <strong>crédito o cargo adicional</strong> según la diferencia de precio entre los planes.</li>
                                    <li>Su próxima facturación reflejará el nuevo precio del plan seleccionado.</li>
                                    <li>Si tiene un <strong>límite de citas alcanzado</strong>, al actualizar a un plan superior podrá seguir agendando citas.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="{{ route('suscriptions.show') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Mi Suscripción
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Contacto Empresarial -->
    <div id="enterpriseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
            <button onclick="closeEnterpriseModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>

            <h3 style="margin-bottom: 10px; color: #333;">Solicitud de Upgrade - Plan Empresarial</h3>
            <p style="color: #666; margin-bottom: 25px;">Un representante de ventas se pondrá en contacto contigo para ayudarte con el proceso de upgrade.</p>

            <form id="enterpriseForm" action="{{ route('enterprise.lead.store') }}" method="POST">
                @csrf
                {{-- Campos hidden con información del cliente y suscripción actual --}}
                <input type="hidden" name="client_id" value="{{ $subscription->client_id }}">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="current_subscription_id" value="{{ $subscription->id }}">
                <input type="hidden" name="current_package_id" value="{{ $subscription->package_id }}">
                <input type="hidden" name="requested_package_id" value="4">
                <input type="hidden" name="source" value="upgrade_request">

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre Completo *</label>
                    <input type="text" name="full_name" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email *</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Teléfono *</label>
                        <input type="tel" name="phone" value="{{ $subscription->client->whatsapp ?? auth()->user()->phone }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre de la Empresa/Clínica *</label>
                        <input type="text" name="company_name" value="{{ $subscription->client->long_name ?? $subscription->client->name }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">¿Cuántos médicos tiene?</label>
                        <input type="number" name="number_of_doctors" min="1" max="10000" placeholder="Ej: 5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">¿Quieren usar el Agente SAMI o un agente personalizado?</label>
                    <div style="display: flex; gap: 20px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: 400; margin: 0;">
                            <input type="radio" name="agent_preference" value="sami" style="margin-right: 8px; cursor: pointer; width: auto;">
                            <span>Agente SAMI</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; font-weight: 400; margin: 0;">
                            <input type="radio" name="agent_preference" value="personalized" style="margin-right: 8px; cursor: pointer; width: auto;">
                            <span>Agente Personalizado</span>
                        </label>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">¿Cuántas sucursales quiere registrar?</label>
                    <input type="number" name="number_of_branches" min="1" max="1000" placeholder="Ej: 3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">¿Qué sistema usa actualmente para la gestión de su clínica?</label>
                    <input type="text" name="current_system" maxlength="255" placeholder="Ej: Microsoft Excel, MediSoft, etc." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                {{-- Información del plan actual --}}
                <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                    <h6 style="margin-bottom: 10px; font-weight: 600;">Plan Actual:</h6>
                    <p style="margin: 0; color: #666;">
                        <strong>{{ $subscription->package->name }}</strong> -
                        ${{ number_format($subscription->package->base_price, 2) }} / {{ ucfirst($subscription->package->billing_period->value) }}
                    </p>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Cuéntanos sobre tus necesidades</label>
                    <textarea name="message" rows="4" placeholder="Ejemplo: Necesitamos aumentar el número de usuarios, agregar más médicos, etc." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Funciones para el modal empresarial
        function openEnterpriseModal() {
            document.getElementById('enterpriseModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeEnterpriseModal() {
            document.getElementById('enterpriseModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer click fuera
        document.getElementById('enterpriseModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEnterpriseModal();
        });

        // Manejar submit del formulario
        document.getElementById('enterpriseForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    alert('¡Gracias! Nos pondremos en contacto contigo pronto.');
                    closeEnterpriseModal();
                    this.reset();
                } else if (response.status === 422) {
                    // Errores de validación
                    let errorMessage = 'Por favor corrige los siguientes errores:\n\n';
                    if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            errorMessage += '• ' + messages.join('\n• ') + '\n';
                        }
                    } else {
                        errorMessage = data.message || 'Hubo un error de validación.';
                    }
                    alert(errorMessage);
                } else {
                    alert(data.message || 'Hubo un error. Por favor intenta nuevamente.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión. Por favor intenta más tarde.');
            }
        });
    </script>
    @endpush
</x-app-layout>
