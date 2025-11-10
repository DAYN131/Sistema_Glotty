<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->renameColumn('numero_aula', 'nombre_aula');
            $table->string('nombre_aula', 20)->change(); // Aumentar longitud
        });
    }

    public function down()
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->renameColumn('nombre_aula', 'numero_aula');
            $table->integer('numero_aula')->change();
        });
    }
};