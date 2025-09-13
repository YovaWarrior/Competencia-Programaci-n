<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_recompensas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recompensa_id')->constrained()->onDelete('cascade');
            $table->datetime('fecha_canje');
            $table->enum('estado', ['canjeada', 'usada', 'vencida']);
            $table->datetime('fecha_uso')->nullable();
            $table->string('codigo_canje', 20)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_recompensas');
    }
};