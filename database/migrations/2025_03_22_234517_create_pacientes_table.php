<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido_paterno', 255);
            $table->string('apellido_materno', 255);
            $table->integer('edad');
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro']);
            $table->char('telefono', 9)->unique();
            $table->string('direccion', 255);
            $table->char('dni', 8)->unique();
            $table->date('fecha_nacimiento');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
};
