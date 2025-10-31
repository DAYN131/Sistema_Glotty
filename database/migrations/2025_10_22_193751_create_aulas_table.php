<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->string('id_aula')->primary();
            $table->string('edificio');
            $table->integer('numero_aula');
            $table->integer('capacidad');
            $table->string('tipo_aula')->default('regular');
            $table->timestamps();
            
            $table->unique(['edificio', 'numero_aula']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('aulas');
    }
};