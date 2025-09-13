<?php
// app/Models/User.php (MODIFICAR EL EXISTENTE)
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'dpi', 'nombre', 'apellido', 'email', 'password', 'telefono', 
        'fecha_nacimiento', 'foto', 'rol', 'activo', 'puntos_verdes', 'co2_reducido_total', 'motivo_suspension'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
        'password' => 'hashed',
        'activo' => 'boolean',
        'puntos_verdes' => 'integer',
        'co2_reducido_total' => 'decimal:2'
    ];

    // Relaciones
    public function membresias()
    {
        return $this->hasMany(UserMembresia::class);
    }

    public function membresiaActiva()
    {
        return $this->hasOne(UserMembresia::class)->where('activa', true)->latest();
    }

    public function rutas()
    {
        return $this->hasMany(Ruta::class);
    }

    public function usosBicicletas()
    {
        return $this->hasMany(UsoBicicleta::class);
    }

    public function reportesDanos()
    {
        return $this->hasMany(ReporteDano::class);
    }

    public function recompensas()
    {
        return $this->hasMany(UserRecompensa::class);
    }

    // Métodos auxiliares
    public function esAdmin()
    {
        return $this->rol === 'administrador';
    }

    public function tieneMembresiaActiva()
    {
        return $this->membresiaActiva()->exists();
    }

    public function puedeUsarBicicleta($tipo)
    {
        $membresia = $this->membresiaActiva;
        if (!$membresia) return false;
        
        return $membresia->membresia->tipo_bicicleta === 'ambas' || 
               $membresia->membresia->tipo_bicicleta === $tipo;
    }
}