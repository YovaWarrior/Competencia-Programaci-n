<?php
// app/Models/Membresia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'tipo_bicicleta', 'duracion', 'precio',
        'duracion_dias', 'minutos_incluidos', 'tarifa_minuto_extra', 'beneficios', 'activa'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'tarifa_minuto_extra' => 'decimal:2',
        'beneficios' => 'array',
        'activa' => 'boolean'
    ];

    public function userMembresias()
    {
        return $this->hasMany(UserMembresia::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_membresias');
    }
}
