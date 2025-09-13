<?php
// app/Models/Recompensa.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'puntos_requeridos', 'tipo', 'valor',
        'stock', 'activa', 'fecha_vencimiento', 'imagen'
    ];

    protected $casts = [
        'puntos_requeridos' => 'integer',
        'valor' => 'decimal:2',
        'stock' => 'integer',
        'activa' => 'boolean',
        'fecha_vencimiento' => 'date'
    ];

    public function userRecompensas()
    {
        return $this->hasMany(UserRecompensa::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_recompensas');
    }

    public function estaDisponible()
    {
        return $this->activa && 
               $this->stock > 0 && 
               (!$this->fecha_vencimiento || $this->fecha_vencimiento > now());
    }

    public function puedeSerCanjeada($puntosUsuario)
    {
        return $this->estaDisponible() && $puntosUsuario >= $this->puntos_requeridos;
    }
}
