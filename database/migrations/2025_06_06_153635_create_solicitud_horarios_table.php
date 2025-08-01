<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud_horarios', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relación con el médico (foreing key)
            $table->unsignedBigInteger('medico_id');
            $table->foreign('medico_id')
                  ->references('id')
                  ->on('medicos')
                  ->onDelete('cascade');

            // Acción solicitada: 'crear' o 'editar'
            $table->string('accion', 20);

            // Estado de la solicitud: pendiente, aprobado, rechazado
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                  ->default('pendiente');

            // timestamps para created_at / updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitud_horarios');
    }
};
