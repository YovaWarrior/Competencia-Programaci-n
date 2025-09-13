<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dpi', 13)->unique()->after('id');
            $table->string('nombre')->after('dpi');
            $table->string('apellido')->after('nombre');
            $table->string('telefono', 15)->after('email');
            $table->date('fecha_nacimiento')->after('telefono');
            $table->string('foto')->nullable()->after('fecha_nacimiento');
            $table->enum('rol', ['usuario', 'administrador'])->default('usuario')->after('foto');
            $table->boolean('activo')->default(true)->after('rol');
            $table->integer('puntos_verdes')->default(0)->after('activo');
            $table->decimal('co2_reducido_total', 10, 2)->default(0)->after('puntos_verdes');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dpi', 'nombre', 'apellido', 'telefono', 'fecha_nacimiento', 
                'foto', 'rol', 'activo', 'puntos_verdes', 'co2_reducido_total'
            ]);
        });
    }
};