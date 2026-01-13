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
                                            <form action="{{ route('suscriptions.upgrade.process') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                <input type="hidden" name="prorate" value="1">
                                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('¿Está seguro que desea actualizar a este plan? Se calculará el prorrateo correspondiente.')">
                                                    <i class="fas fa-arrow-up me-2"></i>Actualizar a este Plan
                                                </button>
                                            </form>
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
</x-app-layout>
