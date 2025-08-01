<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToMedicosTable extends Migration
{
    public function up()
    {
        Schema::table('medicos', function (Blueprint $table) {
            // Agrega la columna user_id como clave foránea (opcionalmente nullable o unique según tu diseño)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            // Si quieres definir constraint con users:
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('medicos', function (Blueprint $table) {
            // Primero elimina la llave foránea (si la definiste)
            $table->dropForeign(['user_id']);
            // Luego elimina la columna
            $table->dropColumn('user_id');
        });
    }
}
