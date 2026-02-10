@props([
    'name' => 'phone',
    'id' => null,
    'value' => '',
    'required' => false,
    'disabled' => false,
    'wireModel' => null,
    'error' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error && (is_string($error) || (is_array($error) && count($error) > 0));
@endphp

<div class="phone-input-wrapper">
    <!-- Input visible para el número de teléfono -->
    <input
        type="phone"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        autocomplete="new-password"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >

    <!-- Mensaje de error -->
    @if($hasError)
        <span class="error phone-error-message" style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">
            @if(is_array($error))
                {{ $error[0] }}
            @else
                {{ $error }}
            @endif
        </span>
    @endif
</div>

<style>
    .phone-input-wrapper {
        position: relative;
        width: 100%;
    }

    /* Asegurar que el contenedor de intl-tel-input ocupe todo el ancho */
    .phone-input-wrapper .iti {
        width: 100% !important;
        display: block !important;
    }

    /* Ajustar el input dentro de intl-tel-input */
    .phone-input-wrapper .iti__input {
        width: 100% !important;
        padding-left: 52px !important; /* Espacio para la bandera y código */
    }

    /* Posicionar correctamente el selector de país */
    .phone-input-wrapper .iti__selected-dial-code {
        margin-left: 6px;
    }
</style>
