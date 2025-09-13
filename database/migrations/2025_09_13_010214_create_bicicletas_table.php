<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bicicletas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 15)->unique();
            $table->enum('tipo', ['tradicional', 'electrica']);
            $table->string('marca');
            $table->string('modelo');
            $table->year('ano_fabricacion');
            $table->enum('estado', ['disponible', 'en_uso', 'mantenimiento', 'danada', 'fuera_servicio']);
            $table->foreignId('estacion_actual_id')->nullable()->constrained('estaciones');
            $table->integer('nivel_bateria')->nullable();
            $table->decimal('kilometraje_total', 10, 2)->default(0);
            $table->datetime('ultimo_mantenimiento')->nullable();
            $table->datetime('proximo_mantenimiento')->nullable();
            $table->json('reportes_danos')->nullable();
            $table->boolean('bloqueada')->default(false);
            $table->text('motivo_bloqueo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bicicletas');
    }
};