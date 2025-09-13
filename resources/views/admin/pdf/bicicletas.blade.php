<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Bicicletas - EcoBici</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; color: #28a745; }
        .subtitle { color: #666; margin-top: 5px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Catálogo de Bicicletas Registradas</div>
    </div>

    <div class="info">
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de bicicletas:</strong> {{ $bicicletas->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Estado</th>
                <th>Estación Actual</th>
                <th>Nivel Batería</th>
                <th>Kilometraje</th>
                <th>Total Usos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bicicletas as $bicicleta)
            <tr>
                <td><strong>{{ $bicicleta->codigo }}</strong></td>
                <td>
                    @if($bicicleta->tipo == 'electrica')
                        <span class="badge badge-success">Eléctrica</span>
                    @else
                        <span class="badge badge-info">Tradicional</span>
                    @endif
                </td>
                <td>{{ $bicicleta->marca }}</td>
                <td>{{ $bicicleta->modelo }}</td>
                <td class="text-center">{{ $bicicleta->ano_fabricacion }}</td>
                <td>
                    @if($bicicleta->estado == 'disponible')
                        <span class="badge badge-success">Disponible</span>
                    @elseif($bicicleta->estado == 'en_uso')
                        <span class="badge badge-warning">En Uso</span>
                    @elseif($bicicleta->estado == 'mantenimiento')
                        <span class="badge badge-info">Mantenimiento</span>
                    @else
                        <span class="badge badge-danger">Dañada</span>
                    @endif
                </td>
                <td>{{ $bicicleta->estacionActual ? $bicicleta->estacionActual->nombre : 'Sin estación' }}</td>
                <td class="text-center">{{ $bicicleta->nivel_bateria ? $bicicleta->nivel_bateria . '%' : 'N/A' }}</td>
                <td class="text-center">{{ number_format($bicicleta->kilometraje_total, 1) }} km</td>
                <td class="text-center">{{ $bicicleta->uso_bicicletas_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema EcoBici - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
