@props(['href' => '#', 'type' => 'primary', 'icon' => ''])

@php
    $typeClass = match($type) {
        'secondary' => 'btn-secondary',
        'success' => 'btn-success', 
        'danger' => 'btn-danger',
        default => ''
    };
@endphp

<div style="text-align: center; margin: 30px 0;">
    <a href="{!! $href !!}" class="btn {{ $typeClass }}">
        @if($icon) {{ $icon }} @endif
        {{ $slot }}
    </a>
</div>