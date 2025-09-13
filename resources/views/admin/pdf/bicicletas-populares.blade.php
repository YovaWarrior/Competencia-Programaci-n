<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bicicletas Populares - EcoBici</title>
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
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .ranking { font-weight: bold; color: #ffc107; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Reporte de Bicicletas Populares</div>
    </div>

    <div class="info">
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de bicicletas con uso:</strong> {{ $bicicletasPopulares->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Tipo</th>
                <th>Marca/Modelo</th>
                <th>Total Usos</th>
                <th>Distancia Total</th>
                <th>Calificación</th>
                <th>Estación Actual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bicicletasPopulares as $index => $bicicleta)
            <tr>
                <td class="text-center">
                    @if($index < 3)
                        <span class="ranking">{{ $index + 1 }}°</span>
                    @else
                        {{ $index + 1 }}
                    @endif
                </td>
                <td><strong>{{ $bicicleta->codigo }}</strong></td>
                <td>
                    @if($bicicleta->tipo == 'electrica')
                        <span class="badge badge-success">Eléctrica</span>
                    @else
                        <span class="badge badge-info">Tradicional</span>
                    @endif
                </td>
                <td>{{ $bicicleta->marca }} {{ $bicicleta->modelo }}</td>
                <td class="text-center">{{ $bicicleta->total_usos }}</td>
                <td class="text-center">{{ number_format($bicicleta->uso_bicicletas_sum_distancia_recorrida ?? 0, 1) }} km</td>
                <td class="text-center">
                    @if($bicicleta->uso_bicicletas_avg_calificacion)
                        {{ number_format($bicicleta->uso_bicicletas_avg_calificacion, 1) }}/5
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $bicicleta->estacionActual ? $bicicleta->estacionActual->nombre : 'Sin estación' }}</td>
                <td>
                    @if($bicicleta->estado == 'disponible')
                        <span class="badge badge-success">Disponible</span>
                    @elseif($bicicleta->estado == 'en_uso')
                        <span class="badge badge-warning">En Uso</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($bicicleta->estado) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema EcoBici - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
