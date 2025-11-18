<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aulas', function (Blueprint $table) {

            // ID autoincremental
            $table->bigIncrements('id');

            // Campos obligatorios
            $table->string('nombre');       // obligatorio
            $table->string('edificio');     // obligatorio
            $table->integer('capacidad');   // obligatorio

            // Tipo de aula flexible
            $table->string('tipo');         // laboratorio, regular, auditorio, etc.

            // Opcionales
            $table->text('equipamiento')->nullable();

            // Disponible en vez de "activo"
            $table->boolean('disponible')->default(true);

            // Timestamps Laravel
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aulas');
    }
};
