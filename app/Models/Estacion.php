<?php
// app/Models/Estacion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estacion extends Model
{
    use HasFactory;

    protected $table = 'estaciones';

    protected $fillable = [
        'nombre', 'codigo', 'descripcion', 'latitud', 'longitud', 'direccion',
        'tipo', 'capacidad_total', 'capacidad_disponible', 'tiene_cargador_electrico',
        'estado', 'horario_operacion'
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'capacidad_total' => 'integer',
        'capacidad_disponible' => 'integer',
        'tiene_cargador_electrico' => 'boolean',
        'horario_operacion' => 'array'
    ];

    public function bicicletas()
    {
        return $this->hasMany(Bicicleta::class, 'estacion_actual_id');
    }

    public function rutasInicio()
    {
        return $this->hasMany(Ruta::class, 'estacion_inicio_id');
    }

    public function rutasFin()
    {
        return $this->hasMany(Ruta::class, 'estacion_fin_id');
    }

    public function usosInicio()
    {
        return $this->hasMany(UsoBicicleta::class, 'estacion_inicio_id');
    }

    public function usosFin()
    {
        return $this->hasMany(UsoBicicleta::class, 'estacion_fin_id');
    }

    public function bicicletasDisponibles()
    {
        return $this->bicicletas()->where('estado', 'disponible');
    }

    public function puedeCargarElectricas()
    {
        return $this->tiene_cargador_electrico && in_array($this->tipo, ['carga', 'mixta']);
    }
}
