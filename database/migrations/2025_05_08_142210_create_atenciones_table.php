<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up()
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade');
            $table->date('fecha_atencion');
            $table->text('diagnostico');
            $table->text('tratamiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('presion_arterial')->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->decimal('temperatura', 5, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down()
    {
        Schema::dropIfExists('atenciones');
    }
};
