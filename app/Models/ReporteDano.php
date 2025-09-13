<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteDano extends Model
{
    use HasFactory;

    protected $table = 'reportes_danos';

    protected $fillable = [
        'user_id', 'bicicleta_id', 'tipo_dano', 'descripcion', 'severidad',
        'fotos', 'estado', 'fecha_reporte', 'fecha_resolucion', 'comentarios_tecnico'
    ];

    protected $casts = [
        'fotos' => 'array',
        'fecha_reporte' => 'datetime',
        'fecha_resolucion' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class);
    }

    public function marcarComoReparado($comentarios = null)
    {
        $this->update([
            'estado' => 'reparado',
            'fecha_resolucion' => now(),
            'comentarios_tecnico' => $comentarios
        ]);
    }
}
