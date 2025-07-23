@props(['items' => []])

<div class="info-grid">
    @foreach($items as $label => $value)
        <div class="info-item">
            <span class="info-label">{{ $label }}:</span>
            <span>{{ $value }}</span>
        </div>
    @endforeach
    {{ $slot }}
</div>