<?php

// app/Models/Bicicleta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bicicleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'tipo', 'marca', 'modelo', 'ano_fabricacion', 'estado',
        'estacion_actual_id', 'nivel_bateria', 'kilometraje_total',
        'ultimo_mantenimiento', 'proximo_mantenimiento', 'reportes_danos',
        'bloqueada', 'motivo_bloqueo'
    ];

    protected $casts = [
        'ano_fabricacion' => 'integer',
        'nivel_bateria' => 'integer',
        'kilometraje_total' => 'decimal:2',
        'ultimo_mantenimiento' => 'datetime',
        'proximo_mantenimiento' => 'datetime',
        'reportes_danos' => 'array',
        'bloqueada' => 'boolean'
    ];

    public function estacionActual()
    {
        return $this->belongsTo(Estacion::class, 'estacion_actual_id');
    }

    public function usosBicicletas()
    {
        return $this->hasMany(UsoBicicleta::class);
    }

    public function reportesDanos()
    {
        return $this->hasMany(ReporteDano::class);
    }

    public function estaDisponible()
    {
        return $this->estado === 'disponible' && !$this->bloqueada;
    }

    public function necesitaMantenimiento()
    {
        return $this->proximo_mantenimiento && $this->proximo_mantenimiento <= now();
    }

    public function nivelBateriaColor()
    {
        if ($this->tipo !== 'electrica') return null;
        
        if ($this->nivel_bateria >= 70) return 'success';
        if ($this->nivel_bateria >= 30) return 'warning';
        return 'danger';
    }
}
