<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtencionesLaboratorioTable extends Migration
{
    public function up()
    {
        Schema::create('atenciones_laboratorio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')
                  ->constrained('atenciones')
                  ->onDelete('cascade');
            $table->decimal('hemoglobina', 5, 2)->nullable();
            $table->integer('leucocitos')->nullable();
            $table->integer('plaquetas')->nullable();
            $table->decimal('colesterol_total', 8, 2)->nullable();
            $table->decimal('trigliceridos', 8, 2)->nullable();
            $table->decimal('glucosa_ayunas', 8, 2)->nullable();
            $table->string('tipo_muestra');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atenciones_laboratorio');
    }
}
