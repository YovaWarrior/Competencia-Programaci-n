<?php
// app/Models/UserRecompensa.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecompensa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'recompensa_id', 'fecha_canje', 'estado', 'fecha_uso', 'codigo_canje'
    ];

    protected $casts = [
        'fecha_canje' => 'datetime',
        'fecha_uso' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class);
    }

    public function marcarComoUsada()
    {
        $this->update([
            'estado' => 'usada',
            'fecha_uso' => now()
        ]);
    }

    public function puedeSerUsada()
    {
        return $this->estado === 'canjeada';
    }
}