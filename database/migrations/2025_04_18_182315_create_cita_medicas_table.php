<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitaMedicasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cita_medicas', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Campos foráneos
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('medico_id');

            // Otros campos
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])
                  ->default('pendiente');

            $table->timestamps();

            // Índices y llaves foráneas
            $table->index('paciente_id');
            $table->index('medico_id');

            $table->foreign('paciente_id')
                  ->references('id')->on('pacientes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('medico_id')
                  ->references('id')->on('medicos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita_medicas');
    }
}
