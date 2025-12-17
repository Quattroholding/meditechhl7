@props([
    'saveKey',
])

@php
    $savedEvent = 'saved-' . $saveKey;
    $errorEvent = 'error-' . $saveKey;
@endphp

<div
    x-data="{
        state: 'idle', // idle | saving | saved | error
        message: '',
        timeout: null,
    }"

    {{-- Inicio del guardado --}}
    x-on:autosave-start.window="
        if ($event.detail === '{{ $saveKey }}') {
            state = 'saving';
            message = '';
            clearTimeout(timeout);
        }
    "

    {{-- Guardado OK --}}
    x-on:{{ $savedEvent }}.window="
        state = 'saved';
        message = '✔ Guardado';
        clearTimeout(timeout);
        timeout = setTimeout(() => state = 'idle', 2000);
    "

    {{-- Error --}}
    x-on:{{ $errorEvent }}.window="
        state = 'error';
        message = $event.detail ?? 'Error al guardar';
        clearTimeout(timeout);
        timeout = setTimeout(() => state = 'idle', 4000);
    "
>
    <!-- SPINNER -->
    <div
        x-show="state === 'saving'"
        x-transition
        x-cloak
        class="mt-1 text-blue-500 flex items-center gap-2"
    >
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: inline-block">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Guardando…</span>
    </div>

    <!-- GUARDADO -->
    <div
        x-show="state === 'saved'"
        x-transition
        x-cloak
        class="mt-1 text-green-600 text-sm"
        x-text="message"
    ></div>

    <!-- ERROR -->
    <div
        x-show="state === 'error'"
        x-transition
        x-cloak
        class="mt-1 text-red-600 text-sm"
        x-text="message"
    ></div>
</div>
