<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CAMBIA ESTE NOMBRE (agrega algo único)
return new class extends Migration  // ← ESTA ES LA SOLUCIÓN
{
    public function up()
    {
        Schema::create('disponibilidad_aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            $table->foreignId('horario_periodo_id')->constrained('horarios_periodo')->onDelete('cascade');
            $table->boolean('disponible')->default(true);
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['aula_id', 'horario_periodo_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('disponibilidad_aulas');
    }
};