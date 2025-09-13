<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reportes_danos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bicicleta_id')->constrained()->onDelete('cascade');
            $table->string('tipo_dano');
            $table->text('descripcion');
            $table->enum('severidad', ['leve', 'moderado', 'severo']);
            $table->json('fotos')->nullable();
            $table->enum('estado', ['reportado', 'en_revision', 'reparado', 'descartado']);
            $table->datetime('fecha_reporte');
            $table->datetime('fecha_resolucion')->nullable();
            $table->text('comentarios_tecnico')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reportes_danos');
    }
};