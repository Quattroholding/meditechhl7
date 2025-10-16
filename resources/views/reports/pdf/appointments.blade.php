<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4472C4;
        }
        .header h1 { font-size: 20px; color: #4472C4; margin-bottom: 5px; }
        .header p { font-size: 8px; color: #666; margin: 2px 0; }
        .filters {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 4px;
            border-left: 4px solid #4472C4;
        }
        .filters strong { font-size: 10px; display: block; margin-bottom: 5px; }
        .filters div { font-size: 8px; margin: 3px 0; padding-left: 10px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #4472C4;
            color: white;
            padding: 8px 4px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 6px 4px;
            border-bottom: 1px solid #ddd;
            font-size: 8px;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f0f0f0; }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #4472C4;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .footer strong { font-size: 10px; color: #4472C4; }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Usuario:</strong> {{ auth()->user()->full_name }}</p>
        @if(auth()->user()->hasRole('doctor'))
            <p><strong>Médico:</strong> {{ auth()->user()->practitioner->profile_name ?? 'N/A' }}</p>
        @endif
    </div>

    @if(count($appliedFilters) > 0)
        <div class="filters">
            <strong>Filtros Aplicados:</strong>
            @foreach($appliedFilters as $key => $value)
                <div>• {{ $key }}: <strong>{{ $value }}</strong></div>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" style="text-align: center; padding: 20px; color: #999;">
                        No se encontraron registros con los filtros aplicados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total de registros:</strong> {{ count($data) }}</p>
        <p style="margin-top: 5px;">Este documento fue generado automáticamente por el Sistema de Reportes</p>
        <p>© {{ date('Y') }} MediTech - Todos los derechos reservados</p>
    </div>
</body>
</html>
