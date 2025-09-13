<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Impacto CO₂ - EcoBici</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌱 EcoBici - Reporte de Impacto CO₂</h1>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $reporteCo2->count() }}</div>
            <div class="stat-label">Viajes Completados</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($reporteCo2->sum('co2_reducido'), 2) }}</div>
            <div class="stat-label">kg CO₂ Reducido</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($reporteCo2->sum('distancia_recorrida'), 1) }}</div>
            <div class="stat-label">km Recorridos</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($reporteCo2->avg('co2_reducido'), 2) }}</div>
            <div class="stat-label">Promedio CO₂/Viaje</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Bicicleta</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Distancia (km)</th>
                <th>CO₂ Reducido (kg)</th>
                <th>Duración</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporteCo2 as $uso)
            <tr>
                <td>{{ $uso->user->nombre }} {{ $uso->user->apellido }}</td>
                <td>{{ $uso->bicicleta->codigo }}</td>
                <td>
                    @if($uso->bicicleta->tipo == 'electrica')
                        <span class="badge badge-success">Eléctrica</span>
                    @else
                        <span class="badge badge-info">Tradicional</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($uso->fecha_hora_inicio)->format('d/m/Y H:i') }}</td>
                <td class="text-center">{{ number_format($uso->distancia_recorrida ?? 0, 1) }}</td>
                <td class="text-center">{{ number_format(abs($uso->co2_reducido), 2) }}</td>
                <td class="text-center">
                    @if($uso->fecha_hora_fin)
                        {{ \Carbon\Carbon::parse($uso->fecha_hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($uso->fecha_hora_fin)) }} min
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>EcoBici</strong> - Sistema de Bicicletas Compartidas</p>
        <p>Contribuyendo a un planeta más verde 🌍</p>
    </div>
</body>
</html>
