<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Activos - EcoBici</title>
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
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Reporte de Usuarios Activos</div>
    </div>

    <div class="info">
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de usuarios activos:</strong> {{ $usuariosActivos->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Membresía</th>
                <th>Total Usos</th>
                <th>CO₂ Reducido</th>
                <th>Puntos Verdes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuariosActivos as $usuario)
            <tr>
                <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                <td>{{ $usuario->email }}</td>
                <td>
                    @if($usuario->membresiaActiva)
                        <span class="badge badge-success">{{ $usuario->membresiaActiva->membresia->nombre }}</span>
                    @else
                        Sin membresía
                    @endif
                </td>
                <td class="text-center">{{ $usuario->uso_bicicletas_count ?? 0 }}</td>
                <td class="text-center">{{ number_format($usuario->uso_bicicletas_sum_co2_reducido ?? 0, 2) }} kg</td>
                <td class="text-center">{{ number_format($usuario->puntos_verdes) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema EcoBici - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
