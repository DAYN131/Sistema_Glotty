<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preregistro_id')->constrained('preregistros');
            $table->decimal('calificacion_1', 3, 1)->nullable();
            $table->decimal('calificacion_2', 3, 1)->nullable();
            $table->decimal('calificacion_3', 3, 1)->nullable();
            $table->decimal('calificacion_4', 3, 1)->nullable();
            $table->decimal('calificacion_5', 3, 1)->nullable();
            $table->decimal('calificacion_6', 3, 1)->nullable();
            $table->decimal('calificacion_final', 3, 1)->nullable();
            $table->decimal('promedio', 3, 1)->nullable();
            $table->boolean('aprobado')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calificaciones');
    }
};