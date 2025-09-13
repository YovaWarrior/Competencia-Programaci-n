<?php
// app/Models/UserMembresia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMembresia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'membresia_id', 'fecha_inicio', 'fecha_fin', 'monto_pagado',
        'estado_pago', 'metodo_pago', 'referencia_pago', 'activa'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'monto_pagado' => 'decimal:2',
        'activa' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membresia()
    {
        return $this->belongsTo(Membresia::class);
    }

    public function usosBicicletas()
    {
        return $this->hasMany(UsoBicicleta::class);
    }

    public function minutosRestantes()
    {
        $usados = $this->usosBicicletas()->sum('minutos_incluidos_usados');
        return max(0, $this->membresia->minutos_incluidos - $usados);
    }

    public function estaVigente()
    {
        return $this->fecha_fin > now() && $this->estado_pago === 'pagado';
    }
}
