@props(['disabled' => false,'functionName'=>'save','saved'])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'form-control']) }}>
{{ $slot }}
</textarea>

