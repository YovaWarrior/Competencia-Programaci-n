<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ingresos - EcoBici</title>
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
        .text-right { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .total { background-color: #e9ecef; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🚴‍♂️ EcoBici PuertoBarrios</div>
        <div class="subtitle">Reporte de Ingresos por Membresías</div>
    </div>

    <div class="info">
        <strong>Período:</strong> {{ $fechaInicio }} - {{ $fechaFin }}<br>
        <strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total de transacciones:</strong> {{ $ingresos->count() }}<br>
        <strong>Ingresos totales:</strong> Q{{ number_format($ingresos->sum('monto_pagado'), 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Membresía</th>
                <th>Monto</th>
                <th>Método Pago</th>
                <th>Referencia</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingresos as $ingreso)
            <tr>
                <td>{{ $ingreso->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $ingreso->user->nombre }} {{ $ingreso->user->apellido }}</td>
                <td>{{ $ingreso->membresia->nombre }}</td>
                <td class="text-right">Q{{ number_format($ingreso->monto_pagado, 2) }}</td>
                <td class="text-center">{{ ucfirst($ingreso->metodo_pago) }}</td>
                <td>{{ $ingreso->referencia_pago ?? 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge badge-success">{{ ucfirst($ingreso->estado_pago) }}</span>
                </td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                <td class="text-right"><strong>Q{{ number_format($ingresos->sum('monto_pagado'), 2) }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema EcoBici - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
