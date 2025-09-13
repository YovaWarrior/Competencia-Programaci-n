<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->enum('tipo_bicicleta', ['tradicional', 'electrica', 'ambas']);
            $table->enum('duracion', ['mensual', 'anual']);
            $table->decimal('precio', 8, 2);
            $table->integer('duracion_dias');
            $table->integer('minutos_incluidos')->nullable();
            $table->decimal('tarifa_minuto_extra', 4, 2)->nullable();
            $table->json('beneficios');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membresias');
    }
};