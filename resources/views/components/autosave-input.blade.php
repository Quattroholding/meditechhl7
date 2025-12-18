@props([
    'type' => 'text',              // text | number | textarea | select | itemOption
    'saveMethod',
    'saveKey' => null,
    'value' => null,
    'class' => 'mt-1 block w-full',
    'rows' => 2,
    // select
    'options' => [],
    'name' => null,
    'selected' => null,

    // itemOption
    'itemCode' => null,
    'itemDescription' => null,
    'min_normal'=>null,
    'max_normal'=>null,
])

@php
    $savedEvent = $saveKey ? 'saved-'.$saveKey : 'saved';
    $errorEvent = $saveKey ? 'error-'.$saveKey : 'error';
@endphp

<div
    x-data="{
        state: 'idle', // idle | saving | saved | error
        message: '',
        timeout: null,
        value: @js($value),
        min: @js($min_normal),
        max: @js($max_normal),

        outOfRange() {
            if (this.value === null || this.value === '') return false;
            const v = parseFloat(this.value);
            if (isNaN(v)) return false;
            if (this.min !== null && v < this.min) return true;
            if (this.max !== null && v > this.max) return true;
            return false;
        }
    }"


    {{-- INICIO DEL GUARDADO --}}
    @if($type === 'select')
        x-on:change="
            state = 'saving';
            message = '';
            clearTimeout(timeout);
        "
    @elseif($type !== 'itemOption')
        x-on:input="
            state = 'saving';
            message = '';
            clearTimeout(timeout);
        "
    @endif

    {{-- GUARDADO OK --}}
    x-on:{{ $savedEvent }}.window="
        state = 'saved';
        message = '✔ Guardado';
        clearTimeout(timeout);
        timeout = setTimeout(() => state = 'idle', 2000);
    "

    {{-- ERROR --}}
    x-on:{{ $errorEvent }}.window="
        state = 'error';
        message = $event.detail ?? 'Error al guardar';
        clearTimeout(timeout);
        timeout = setTimeout(() => state = 'idle', 4000);
    "
>

    {{-- TEXTAREA --}}
    @if($type === 'textarea')
        <textarea
            wire:key="autosave-input-{{ $saveKey }}"
            id="{{\Illuminate\Support\Str::uuid()}}"
            {{ $attributes }}
            class="{{ $class }}"
            rows="{{ $rows }}"
        >{{ $value }}</textarea>

        {{-- SELECT --}}
    @elseif($type === 'select')
        <select
            id="{{\Illuminate\Support\Str::uuid()}}"
            {{ $attributes }}
            name="{{ $name }}"
            class="{{ $class }}"
        >
            <option value="">Seleccione</option>
            @foreach ($options as $value => $label)
                <option
                    value="{{ $value }}"
                    @if(is_array($selected) ? in_array($value, $selected) : $value == $selected) selected @endif
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        {{-- ITEM OPTION (SOLO VISUAL, GUARDADO POR CLICK EXTERNO) --}}
    @elseif($type === 'itemOption')
        <div class="{{ $class }}">
            <div class="font-semibold">{{ $itemCode }}</div>
            <div class="text-sm">{{ $itemDescription }}</div>
        </div>

        {{-- INPUT NORMAL --}}
    @else
        <input
            wire:key="autosave-input-{{ $saveKey }}"
            id="{{\Illuminate\Support\Str::uuid()}}"
            type="{{ $type }}"
            {{ $attributes }}
            class="{{ $class }}"
            value="{{ $value }}"
            :class="outOfRange()? 'bg-red-50 border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
        />
    @endif

    {{-- SPINNER --}}
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

    {{-- GUARDADO --}}
    <div
        x-show="state === 'saved'"
        x-transition
        x-cloak
        class="mt-1 text-green-600 text-sm"
        x-text="message"
    ></div>

    {{-- ERROR --}}
    <div
        x-show="state === 'error'"
        x-transition
        x-cloak
        class="mt-1 text-red-600 text-sm"
        x-text="message"
    ></div>
</div>
