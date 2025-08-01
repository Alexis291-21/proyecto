<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_horarios_table.php
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade');
            $table->string('dia');
            $table->time('turno_manana_inicio')->nullable();
            $table->time('turno_manana_fin')->nullable();
            $table->time('turno_tarde_inicio')->nullable();
            $table->time('turno_tarde_fin')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
