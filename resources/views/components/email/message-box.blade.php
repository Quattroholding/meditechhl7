@props(['type' => 'welcome', 'title' => '', 'icon' => ''])

@php
    $classes = [
        'welcome' => 'welcome-message',
        'success' => 'success-box',
        'warning' => 'warning-box',
        'info' => 'info-box',
        'highlight' => 'highlight-box'
    ];
    
    $boxClass = $classes[$type] ?? 'welcome-message';
@endphp

<div class="{{ $boxClass }}">
    @if($title)
        <h3>
            @if($icon) {{ $icon }} @endif
            {{ $title }}
        </h3>
    @endif
    {{ $slot }}
</div>