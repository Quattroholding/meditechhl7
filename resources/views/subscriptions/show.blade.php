<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Plan de Suscripción
                @endslot
                @slot('li_1')
                    Mi Suscripción
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <!-- Subscription Overview -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Plan Actual</h4>

                            @if($subscription)
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-primary">{{ $subscription->package->name }}</h5>
                                        <p class="text-muted">{{ $subscription->package->description }}</p>
                                        <p class="text-muted">Usuarios: {{ $subscription->package->max_users }}</p>
                                        <p class="text-muted">Agente de Whatsapp (SAMI): {{ $subscription->package->agent_available ? 'SI' : 'NO' }}</p>
                                        @if($subscription->package->appointments_limit)
                                            <p class="text-muted">
                                                Límite de Citas:
                                                <strong>{{ auth()->user()->getAppointmentsCountInCurrentPeriod() }} / {{ $subscription->package->appointments_limit }}</strong>
                                                @if(auth()->user()->hasReachedAppointmentsLimit())
                                                    <span class="badge bg-danger ms-2">Límite Alcanzado</span>
                                                @else
                                                    <span class="badge bg-success ms-2">{{ auth()->user()->getRemainingAppointments() }} restantes</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-muted">Límite de Citas: <strong class="text-success">Ilimitado</strong></p>
                                        @endif
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <span class="badge bg-{{ $subscription->status->color() }} fs-5">
                                            {{ $subscription->status->label() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Precio Base</label>
                                            <h5>${{ number_format($subscription->package->base_price, 2) }}</h5>
                                        </div>
                                        @if($subscription->extra_doctors_count > 0)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Médicos Adicionales</label>
                                                <h5>{{ $subscription->extra_doctors_count }} x ${{ number_format($subscription->package->price_per_extra_doctor, 2) }}</h5>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($subscription->current_period_starts_at && $subscription->current_period_ends_at)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Período Actual</label>
                                                <h6>{{ $subscription->current_period_starts_at->format('d/m/Y') }} - {{ $subscription->current_period_ends_at->format('d/m/Y') }}</h6>
                                            </div>
                                        @endif
                                        @if($subscription->next_billing_date)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Próxima Facturación</label>
                                                <h6>{{ $subscription->next_billing_date->format('d/m/Y') }}</h6>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($subscription->package->features && count($subscription->package->features) > 0)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Características del Plan</h5>
                                        <ul class="list-unstyled">
                                            @foreach($subscription->package->features as $feature)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="text-end">
                                    @can('suscriptions.show')
                                        <a href="{{ route('suscriptions.upgrade') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-up me-2"></i>Actualizar Plan
                                        </a>
                                    @endcan
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No tienes una suscripción activa</h5>
                                    <p class="text-muted">Contacta con el administrador para activar tu plan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Summary -->
                <div class="col-md-4">
                    @if($subscription)
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-white">Total Mensual</h5>
                                <h2 class="text-white">${{ number_format($subscription->calculateCurrentPrice(), 2) }}</h2>
                                <small>{{ ucfirst($subscription->package->billing_period->value) }}</small>
                            </div>
                        </div>

                        @if($subscription->currentInvoice)
                            <div class="card border-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Factura Pendiente</h5>
                                    <p class="mb-2">
                                        <strong>{{ $subscription->currentInvoice->invoice_number }}</strong>
                                    </p>
                                    <p class="mb-2">
                                        Monto: <strong>${{ number_format($subscription->currentInvoice->total, 2) }}</strong>
                                    </p>
                                    <p class="mb-3">
                                        Vence: <strong>{{ $subscription->currentInvoice->due_date->format('d/m/Y') }}</strong>
                                    </p>
                                    <a href="{{ route('suscriptions.invoices.show', $subscription->currentInvoice->id) }}" class="btn btn-warning btn-sm w-100">
                                        Ver Factura
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
