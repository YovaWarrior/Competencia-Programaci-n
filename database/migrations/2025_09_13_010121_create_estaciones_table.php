<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('estaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 10)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->string('direccion');
            $table->enum('tipo', ['carga', 'descanso', 'seleccion', 'mixta']);
            $table->integer('capacidad_total');
            $table->integer('capacidad_disponible');
            $table->boolean('tiene_cargador_electrico')->default(false);
            $table->enum('estado', ['activa', 'mantenimiento', 'fuera_servicio'])->default('activa');
            $table->json('horario_operacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estaciones');
    }
};