@auth
    @php
        $message = auth()->user()->getSubscriptionStatusMessage();
        $alertType = auth()->user()->getSubscriptionAlertType();
        $daysRemaining = auth()->user()->getDaysUntilSubscriptionAction();
        $subscription = auth()->user()->getActiveSubscription();
    @endphp
    @props(['showFirstCol' => true])
    @if($message && $alertType)
        <div class="subscription-alert-container">
            <div class="alert alert-{{ $alertType }} alert-dismissible fade show mb-0 border-0 rounded-0" role="alert" @if($showFirstCol) style="position: sticky; margin-top: 70px; z-index: 999;" @endif>
                <div class="container-fluid">
                    <div class="row align-items-center">
                        @if($showFirstCol)
                        <div class="col-md-2"></div>
                        @endif
                        <div class="@if($showFirstCol) col-md-7 @else col-md-9 @endif">
                            <div class="d-flex align-items-center">
                                @if($alertType === 'danger')
                                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                @elseif($alertType === 'warning')
                                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                                @else
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                @endif
                                <div>
                                    <strong>
                                        @if(auth()->user()->hasReachedAppointmentsLimit())
                                            ¡Límite de Citas Alcanzado!
                                        @elseif($subscription && $subscription->status->value === 'suspended')
                                            ¡Suscripción Suspendida!
                                        @elseif($subscription && $subscription->status->value === 'expired')
                                            ¡Suscripción Expirada!
                                        @elseif($subscription && $subscription->status->value === 'past_due')
                                            Pago Pendiente
                                        @elseif($subscription && $subscription->status->value === 'pending_activation')
                                            Activación Pendiente
                                        @elseif($subscription && $subscription->status->value === 'trial')
                                            Período de Prueba
                                        @else
                                            Aviso de Suscripción
                                        @endif
                                    </strong>
                                    <br>
                                    {{ $message }}

                                    @if($daysRemaining !== null && $daysRemaining >= 0)
                                        <br>
                                        <span class="badge bg-{{ $daysRemaining <= 3 ? 'danger' : 'warning' }} mt-1">
                                            @if($subscription && $subscription->status->value === 'suspended')
                                                {{ $daysRemaining }} {{ Str::plural('día', $daysRemaining) }} restantes antes de la expiración permanente
                                            @elseif($subscription && $subscription->status->value === 'past_due')
                                                {{ $daysRemaining }} {{ Str::plural('día', $daysRemaining) }} restantes antes de la suspensión
                                            @elseif($subscription && $subscription->status->value === 'trial')
                                                {{ $daysRemaining }} {{ Str::plural('día', $daysRemaining) }} restantes de prueba
                                            @endif
                                        </span>
                                    @endif

                                    @if(auth()->user()->hasReachedAppointmentsLimit())
                                        <div class="mt-2">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                El agendamiento de citas está <strong>deshabilitado</strong> hasta que actualice su plan o se renueve su período de suscripción.
                                            </small>
                                        </div>
                                    @elseif($subscription && in_array($subscription->status->value, ['suspended', 'past_due', 'pending_activation']))
                                        <div class="mt-2">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                @if($subscription->status->value === 'suspended')
                                                    El agendamiento de citas está <strong>deshabilitado</strong>. Regularice su subscripción para continuar usando el servicio.
                                                @elseif($subscription->status->value === 'past_due')
                                                    Si no realiza el pago dentro del período de gracia, su suscripción será <strong>suspendida</strong> automáticamente.
                                                @else
                                                    Complete el pago de su factura para activar su suscripción y comenzar a <strong>agendar citas</strong>.
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            @can('suscriptions.invoices.index')
                                @if($subscription && in_array($subscription->status->value, ['suspended', 'past_due', 'pending_activation']))
                                    <a href="{{ route('suscriptions.invoices.index') }}" class="btn btn-light btn-sm me-2">
                                        <i class="fas fa-file-invoice me-1"></i>Ver Facturas
                                    </a>
                                @endif
                            @endcan
                            @can('suscriptions.show')
                                @if(auth()->user()->hasReachedAppointmentsLimit())
                                    <a href="{{ route('suscriptions.show') }}" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-arrow-up me-1"></i>Actualizar Plan
                                    </a>
                                @endif
                                <a href="{{ route('suscriptions.show') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-cog me-1"></i>Mi Suscripción
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
@endauth
