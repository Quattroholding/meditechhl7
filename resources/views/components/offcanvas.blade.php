@props([
    'id' => null,
    'title' => '',
    'position' => 'right',
    'backdrop' => true,
    'scroll' => false,
    'size' => 'md'
])

@php
    $positionClass = match($position) {
        'top' => 'offcanvas-top',
        'bottom' => 'offcanvas-bottom',
        'left' => 'offcanvas-start',
        default => 'offcanvas-end'
    };

    $sizeClass = match($size) {
        'sm' => in_array($position, ['top', 'bottom']) ? 'offcanvas-size-sm-v' : 'offcanvas-size-sm',
        'lg' => in_array($position, ['top', 'bottom']) ? 'offcanvas-size-lg-v' : 'offcanvas-size-lg',
        'xl' => in_array($position, ['top', 'bottom']) ? 'offcanvas-size-xl-v' : 'offcanvas-size-xl',
        default => in_array($position, ['top', 'bottom']) ? 'offcanvas-size-md-v' : 'offcanvas-size-md'
    };
@endphp

<div class="offcanvas {{ $positionClass }} {{ $sizeClass }}"
     tabindex="-1"
     id="{{ $id }}"
     @if(!$backdrop) data-bs-backdrop="false" @endif
     @if($scroll) data-bs-scroll="true" @endif
     aria-labelledby="{{ $id }}Label">

    <div class="offcanvas-header">
        @if($title)
            <h5 class="offcanvas-title" id="{{ $id }}Label">{{ $title }}</h5>
        @endif
        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        {{ $slot }}
    </div>
</div>

<style>
    /* Tamaños personalizados para offcanvas */
    .offcanvas-size-sm { width: 250px !important; }
    .offcanvas-size-md { width: 350px !important; }
    .offcanvas-size-lg { width: 450px !important; }
    .offcanvas-size-xl { width: 550px !important; }

    .offcanvas-size-sm-v { height: 200px !important; }
    .offcanvas-size-md-v { height: 300px !important; }
    .offcanvas-size-lg-v { height: 400px !important; }
    .offcanvas-size-xl-v { height: 500px !important; }

    /* Animaciones mejoradas */
    .offcanvas {
        transition: all 0.3s ease-in-out;
    }

    .offcanvas-start {
        border-right: 1px solid rgba(0,0,0,.125);
    }

    .offcanvas-end {
        border-left: 1px solid rgba(0,0,0,.125);
    }

    .offcanvas-top {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }

    .offcanvas-bottom {
        border-top: 1px solid rgba(0,0,0,.125);
    }
</style>
