<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uso_bicicletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bicicleta_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_membresia_id')->constrained()->onDelete('cascade');
            $table->foreignId('estacion_inicio_id')->constrained('estaciones');
            $table->foreignId('estacion_fin_id')->nullable()->constrained('estaciones');
            $table->foreignId('ruta_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('fecha_hora_inicio');
            $table->datetime('fecha_hora_fin')->nullable();
            $table->decimal('distancia_recorrida', 8, 2)->nullable();
            $table->integer('duracion_minutos')->nullable();
            $table->integer('minutos_incluidos_usados')->default(0);
            $table->integer('minutos_extra')->default(0);
            $table->decimal('costo_extra', 8, 2)->default(0);
            $table->decimal('co2_reducido', 8, 2)->default(0);
            $table->integer('puntos_verdes_ganados')->default(0);
            $table->enum('estado', ['en_curso', 'completado', 'cancelado'])->default('en_curso');
            $table->text('comentarios')->nullable();
            $table->integer('calificacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('uso_bicicletas');
    }
};