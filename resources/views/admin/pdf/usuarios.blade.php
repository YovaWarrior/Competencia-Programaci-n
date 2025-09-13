<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Usuarios - EcoBici</title>
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
        .badge-secondary { background-color: #e2e3e5; color: #383d41; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Catálogo de Usuarios Registrados</div>
    </div>

    <div class="info">
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de usuarios:</strong> {{ $usuarios->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>DPI</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha Nacimiento</th>
                <th>Membresía</th>
                <th>Usos</th>
                <th>CO₂ Reducido</th>
                <th>Puntos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->dpi }}</td>
                <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->telefono }}</td>
                <td>{{ $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</td>
                <td>
                    @if($usuario->membresiaActiva)
                        {{ $usuario->membresiaActiva->membresia->nombre }}
                    @else
                        Sin membresía
                    @endif
                </td>
                <td class="text-center">{{ $usuario->uso_bicicletas_count ?? 0 }}</td>
                <td class="text-center">{{ number_format($usuario->uso_bicicletas_sum_co2_reducido ?? 0, 2) }} kg</td>
                <td class="text-center">{{ number_format($usuario->puntos_verdes) }}</td>
                <td class="text-center">
                    @if($usuario->activo)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-secondary">Inactivo</span>
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
