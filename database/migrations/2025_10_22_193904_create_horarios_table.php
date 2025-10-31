<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo'); // semanal, sabatino
            $table->json('dias'); // Array de días: ["Lunes", "Miércoles"]
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios');
    }
};