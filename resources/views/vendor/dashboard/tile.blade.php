<div
    style="grid-area: {{ $gridArea }}; {{ $show ? '' : 'display:none' }}"
    {{ $attributes->merge([
        'class'=>'tile-wrapper overflow-hidden rounded relative'
        ])}}
    {{ $refreshIntervalInSeconds ? "wire:poll.{$refreshIntervalInSeconds}s" : ''  }}
>
    <div class="tile-content">
        {{ $slot }}
    </div>
</div>
