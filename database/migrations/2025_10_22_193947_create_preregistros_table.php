<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preregistros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->integer('nivel_solicitado'); // 1,2,3,4,5
            $table->foreignId('horario_solicitado_id')->constrained('horarios');
            $table->string('semestre_carrera')->nullable();
            $table->foreignId('grupo_asignado_id')->nullable()->constrained('grupos');
            $table->enum('estado', ['preregistrado', 'asignado', 'cursando', 'finalizado', 'cancelado'])->default('preregistrado');
            $table->enum('pagado', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preregistros');
    }
};