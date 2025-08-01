<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasColumn('atenciones_pruebas', 'estado')) {
            Schema::table('atenciones_pruebas', function (Blueprint $table) {
                $table->enum('estado', ['En curso', 'Atendida'])
                      ->default('En curso')
                      ->after('tipo_muestra');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('atenciones_pruebas', 'estado')) {
            Schema::table('atenciones_pruebas', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};
