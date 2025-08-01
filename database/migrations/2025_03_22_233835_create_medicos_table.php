<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido_paterno', 255);
            $table->string('apellido_materno', 255);
            $table->string('especialidad');
            $table->char('telefono', 9)->unique();
            $table->char('dni', 8)->unique();
            $table->string('email', 100)->unique();
            $table->enum('disponibilidad', ['Disponible', 'No disponible'])->default('No disponible');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicos');
    }
};
