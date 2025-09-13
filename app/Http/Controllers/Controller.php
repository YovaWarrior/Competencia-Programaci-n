<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Respuesta JSON estándar para APIs
     */
    protected function jsonResponse($data = null, $message = null, $status = 200)
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Respuesta de éxito
     */
    protected function successResponse($data = null, $message = 'Operación exitosa')
    {
        return $this->jsonResponse($data, $message, 200);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse($message = 'Error en la operación', $status = 400, $data = null)
    {
        return $this->jsonResponse($data, $message, $status);
    }

    /**
     * Calcular CO₂ reducido basado en distancia
     */
    protected function calcularCO2($distanciaKm)
    {
        // 0.23 kg CO₂ por kilómetro (promedio auto vs bicicleta)
        return round($distanciaKm * 0.23, 2);
    }

    /**
     * Calcular puntos verdes basado en CO₂ y tipo de bicicleta
     */
    protected function calcularPuntosVerdes($co2Reducido, $tipoBicicleta = 'tradicional')
    {
        $puntos = floor($co2Reducido * 10); // 1 punto por cada 0.1 kg CO₂
        
        // Multiplicador según tipo de bicicleta
        switch ($tipoBicicleta) {
            case 'electrica':
                return floor($puntos * 1.5);
            case 'premium':
                return $puntos * 2;
            default:
                return $puntos;
        }
    }

    /**
     * Obtener equivalencias ambientales del CO₂
     */
    protected function obtenerEquivalenciasCO2($totalCO2)
    {
        return [
            'arboles_plantados' => round($totalCO2 / 21.8, 1), // 1 árbol absorbe 21.8 kg CO₂/año
            'autos_detenidos_un_dia' => round($totalCO2 / 4.6, 1), // Auto promedio emite 4.6 kg CO₂/día
            'km_auto_evitados' => round($totalCO2 / 0.23, 1), // 0.23 kg CO₂ por km en auto
            'energia_casa_dias' => round($totalCO2 / 12, 1), // Casa promedio 12 kg CO₂/día
        ];
    }

    /**
     * Formatear estadísticas para dashboard
     */
    protected function formatearEstadisticas($stats)
    {
        return [
            'recorridos_totales' => number_format($stats['recorridos_totales']),
            'co2_reducido_total' => number_format($stats['co2_reducido_total'], 2) . ' kg',
            'puntos_verdes' => number_format($stats['puntos_verdes']),
            'tiempo_total' => $this->formatearTiempo($stats['tiempo_total_minutos'] ?? 0),
            'distancia_total' => number_format($stats['distancia_total'] ?? 0, 2) . ' km',
        ];
    }

    /**
     * Formatear tiempo en formato legible
     */
    protected function formatearTiempo($minutos)
    {
        if ($minutos < 60) {
            return $minutos . ' min';
        }
        
        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;
        
        if ($horas < 24) {
            return $horas . 'h ' . $minutosRestantes . 'min';
        }
        
        $dias = floor($horas / 24);
        $horasRestantes = $horas % 24;
        
        return $dias . 'd ' . $horasRestantes . 'h';
    }

    /**
     * Validar coordenadas GPS
     */
    protected function validarCoordenadas($latitud, $longitud)
    {
        return $latitud >= -90 && $latitud <= 90 && $longitud >= -180 && $longitud <= 180;
    }

    /**
     * Calcular distancia entre dos puntos GPS (Haversine)
     */
    protected function calcularDistanciaGPS($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radio de la Tierra en km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return round($earthRadius * $c, 2);
    }
}
