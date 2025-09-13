<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membresia_id')->constrained()->onDelete('cascade');
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
            $table->decimal('monto_pagado', 8, 2);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'vencido'])->default('pendiente');
            $table->string('metodo_pago')->nullable();
            $table->string('referencia_pago')->nullable();
            $table->boolean('activa')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_membresias');
    }
};