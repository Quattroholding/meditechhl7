@props(['practitioner'])

@php
    $hemoScreenInfo = $practitioner->getHemoScreenInfo();
@endphp

@if($hemoScreenInfo)
<div {{ $attributes->merge(['class' => 'alert alert-info d-flex align-items-center']) }} role="alert">
    <i class="fas fa-microscope me-3 fs-4"></i>
    <div>
        <strong><i class="fas fa-info-circle"></i> HemoScreen Disponible</strong>
        <p class="mb-0 small">
            {{ $hemoScreenInfo['message'] }}
        </p>
        @if($hemoScreenInfo['last_used_at'])
        <small class="text-muted">
            <i class="fas fa-clock"></i> Último uso: {{ $hemoScreenInfo['last_used_at']->diffForHumans() }}
        </small>
        @endif
    </div>
</div>
@endif
