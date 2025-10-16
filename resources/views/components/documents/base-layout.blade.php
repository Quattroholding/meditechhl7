<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle }}</title>

    @include('documents.partials.styles')

    {{ $additionalStyles ?? '' }}
</head>
<body>
    {{ $slot }}
</body>
</html>
