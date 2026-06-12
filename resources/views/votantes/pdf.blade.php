<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de votantes</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; }
        .header { margin-bottom: 18px; }
        .title { font-size: 22px; font-weight: 700; margin: 0; }
        .subtitle { color: #6b7280; margin: 4px 0 0; }
        .meta { margin: 14px 0 18px; padding: 10px 12px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; }
        .meta span { display: inline-block; margin-right: 18px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 7px 8px; vertical-align: top; }
        th { background: #eef2ff; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .02em; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; }
        .pending { background: #fef3c7; color: #92400e; }
        .confirmed { background: #d1fae5; color: #065f46; }
        .small { font-size: 10px; color: #6b7280; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Reporte de votantes</p>
        <p class="subtitle">Exportado desde Electoral</p>
    </div>

    <div class="meta">
        <span><strong>Total:</strong> {{ $total }}</span>
        <span><strong>Generado:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</span>
        <span><strong>Estado:</strong> {{ $filters['estado'] ?: 'Todos' }}</span>
        <span><strong>Departamento:</strong> {{ $filters['departamento'] ?: 'Todos' }}</span>
        <span><strong>Municipio:</strong> {{ $filters['municipio'] ?: 'Todos' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Ubicación</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($votantes as $votante)
                <tr>
                    <td>
                        <div><strong>{{ $votante->user?->name ?? 'Sin dato' }}</strong></div>
                        <div class="small">{{ $votante->user?->email ?? '' }}</div>
                    </td>
                    <td><span class="badge {{ $votante->estado_registro === 'pendiente' ? 'pending' : 'confirmed' }}">{{ $votante->estado_registro_label }}</span></td>
                    <td>{{ $votante->nombres }} {{ $votante->apellidos }}</td>
                    <td>{{ $votante->tipo_identificacion }} - {{ $votante->numero_identificacion }}</td>
                    <td>{{ $votante->telefono ?? 'Sin dato' }}</td>
                    <td>
                        <div>{{ $votante->departamento ?? 'Sin dato' }} / {{ $votante->municipio ?? 'Sin dato' }}</div>
                        <div class="small">{{ $votante->puesto_votacion ?? 'Sin dato' }} - Mesa {{ $votante->mesa_votacion ?? 'Sin dato' }}</div>
                    </td>
                    <td>{{ optional($votante->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
