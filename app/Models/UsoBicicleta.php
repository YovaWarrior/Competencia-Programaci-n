<?php

// app/Models/UsoBicicleta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsoBicicleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bicicleta_id', 'user_membresia_id', 'estacion_inicio_id',
        'estacion_fin_id', 'ruta_id', 'fecha_hora_inicio', 'fecha_hora_fin',
        'distancia_recorrida', 'duracion_minutos', 'minutos_incluidos_usados',
        'minutos_extra', 'costo_extra', 'co2_reducido', 'puntos_verdes_ganados',
        'estado', 'comentarios', 'calificacion'
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
        'distancia_recorrida' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'minutos_incluidos_usados' => 'integer',
        'minutos_extra' => 'integer',
        'costo_extra' => 'decimal:2',
        'co2_reducido' => 'decimal:2',
        'puntos_verdes_ganados' => 'integer',
        'calificacion' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class);
    }

    public function userMembresia()
    {
        return $this->belongsTo(UserMembresia::class);
    }

    public function estacionInicio()
    {
        return $this->belongsTo(Estacion::class, 'estacion_inicio_id');
    }

    public function estacionFin()
    {
        return $this->belongsTo(Estacion::class, 'estacion_fin_id');
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function calcularCosto()
    {
        if ($this->minutos_extra > 0) {
            $tarifa = $this->userMembresia->membresia->tarifa_minuto_extra;
            return $this->minutos_extra * $tarifa;
        }
        return 0;
    }

    public function estaEnCurso()
    {
        return $this->estado === 'en_curso';
    }
}
