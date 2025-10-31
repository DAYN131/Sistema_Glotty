<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->integer('nivel_ingles'); // 1,2,3,4,5
            $table->string('letra_grupo', 1); // A,B,C
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->foreignId('horario_id')->constrained('horarios');
            $table->string('aula_id')->nullable()->constrained('aulas');
            $table->string('profesor_id')->nullable()->constrained('profesores');
            $table->integer('capacidad_maxima')->default(30);
            $table->integer('estudiantes_inscritos')->default(0);
            $table->enum('estado', ['planificado', 'con_profesor', 'con_aula', 'activo', 'cancelado'])->default('planificado');
            $table->timestamps();
            
            $table->foreign('aula_id')->references('id_aula')->on('aulas');
            $table->foreign('profesor_id')->references('id_profesor')->on('profesores');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos');
    }
};