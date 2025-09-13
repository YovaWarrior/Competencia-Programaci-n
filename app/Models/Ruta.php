<?php


// app/Models/Ruta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nombre', 'descripcion', 'puntos_ruta', 'estacion_inicio_id',
        'estacion_fin_id', 'distancia_km', 'tiempo_estimado_minutos', 'dificultad',
        'favorita', 'veces_usada', 'co2_reducido_estimado'
    ];

    protected $casts = [
        'puntos_ruta' => 'array',
        'distancia_km' => 'decimal:2',
        'tiempo_estimado_minutos' => 'integer',
        'favorita' => 'boolean',
        'veces_usada' => 'integer',
        'co2_reducido_estimado' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estacionInicio()
    {
        return $this->belongsTo(Estacion::class, 'estacion_inicio_id');
    }

    public function estacionFin()
    {
        return $this->belongsTo(Estacion::class, 'estacion_fin_id');
    }

    public function usosBicicletas()
    {
        return $this->hasMany(UsoBicicleta::class);
    }

    public function incrementarUso()
    {
        $this->increment('veces_usada');
    }
}
