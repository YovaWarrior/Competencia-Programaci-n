<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recompensas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->integer('puntos_requeridos');
            $table->enum('tipo', ['descuento', 'tiempo_gratis', 'merchandising', 'experiencia']);
            $table->decimal('valor', 8, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('activa')->default(true);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recompensas');
    }
};