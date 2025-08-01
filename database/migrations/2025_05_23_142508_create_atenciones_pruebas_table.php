<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtencionesPruebasTable extends Migration
{
    public function up()
    {
        Schema::create('atenciones_pruebas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')
                ->constrained('atenciones')
                ->onDelete('cascade');
            $table->string('glucosa')->nullable();
            $table->string('vih')->nullable();
            $table->string('embarazo')->nullable();
            $table->string('covid_19')->nullable();
            $table->string('tipo_muestra')->nullable();
            $table->enum('estado', ['En curso', 'Atendida'])->default('En curso');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atenciones_pruebas');
    }
}
