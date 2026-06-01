@props(['options' => [], 'selected' => null, 'name'])

<select name="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }}>
    <option value="">{{__('generic.select')}}</option>
    @foreach ($options as $key => $option)
        @php
            // Handle Enum instances
            if (is_object($option) && $option instanceof \BackedEnum) {
                $value = $option->value;
                $label = method_exists($option, 'label') ? $option->label() : $option->name;
            } else {
                $value =  $key;
                $label =  $option;
            }
        @endphp
        <option value="{{ $value }}" {{ in_array($value,$selected) ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
