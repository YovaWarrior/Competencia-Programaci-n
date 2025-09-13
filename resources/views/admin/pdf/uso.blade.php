<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Uso - EcoBici</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; color: #28a745; }
        .subtitle { color: #666; margin-top: 5px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 4px; border-radius: 3px; font-size: 9px; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Reporte de Uso de Bicicletas</div>
    </div>

    <div class="info">
        <strong>Período:</strong> {{ $fechaInicio }} - {{ $fechaFin }}<br>
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de usos:</strong> {{ $usos->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Bicicleta</th>
                <th>Estación Inicio</th>
                <th>Estación Fin</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Duración</th>
                <th>Distancia</th>
                <th>CO₂ Reducido</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usos as $uso)
            <tr>
                <td>{{ $uso->id }}</td>
                <td>{{ $uso->user->nombre }} {{ $uso->user->apellido }}</td>
                <td><span class="badge badge-info">{{ $uso->bicicleta->codigo }}</span></td>
                <td>{{ $uso->estacionInicio->nombre ?? 'N/A' }}</td>
                <td>{{ $uso->estacionFin->nombre ?? 'N/A' }}</td>
                <td>{{ $uso->fecha_hora_inicio ? $uso->fecha_hora_inicio->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>{{ $uso->fecha_hora_fin ? $uso->fecha_hora_fin->format('d/m/Y H:i') : 'En curso' }}</td>
                <td class="text-center">{{ $uso->duracion_minutos ?? 0 }} min</td>
                <td class="text-center">{{ $uso->distancia_recorrida ? number_format($uso->distancia_recorrida, 2) . ' km' : 'N/A' }}</td>
                <td class="text-center">{{ number_format($uso->co2_reducido ?? 0, 2) }} kg</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema EcoBici - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
