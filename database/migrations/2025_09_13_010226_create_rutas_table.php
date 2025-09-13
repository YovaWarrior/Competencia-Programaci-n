<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->json('puntos_ruta');
            $table->foreignId('estacion_inicio_id')->constrained('estaciones');
            $table->foreignId('estacion_fin_id')->constrained('estaciones');
            $table->decimal('distancia_km', 8, 2);
            $table->integer('tiempo_estimado_minutos');
            $table->enum('dificultad', ['facil', 'moderada', 'dificil'])->default('facil');
            $table->boolean('favorita')->default(false);
            $table->integer('veces_usada')->default(0);
            $table->decimal('co2_reducido_estimado', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rutas');
    }
};