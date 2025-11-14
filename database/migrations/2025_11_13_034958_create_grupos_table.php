<?php
// database/migrations/2024_01_01_000008_create_grupos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposTable extends Migration
{
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('nivel_ingles');
            $table->string('letra_grupo', 1);
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->foreignId('horario_periodo_id')->constrained('horarios_periodo')->onDelete('cascade');
            $table->string('aula_id')->nullable()->constrained('aulas')->onDelete('set null');
            $table->string('profesor_id')->nullable()->constrained('profesores')->onDelete('set null');
            $table->integer('capacidad_maxima')->default(30);
            $table->integer('estudiantes_inscritos')->default(0);
            $table->enum('estado', ['planificado', 'con_profesor', 'con_aula', 'activo', 'cancelado'])->default('planificado');
            $table->timestamps();
            
            // Validaciones de unicidad para evitar sobrelapos
            $table->unique(['periodo_id', 'nivel_ingles', 'letra_grupo']);
            $table->unique(['periodo_id', 'horario_periodo_id', 'aula_id']);
            $table->unique(['periodo_id', 'horario_periodo_id', 'profesor_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos');
    }
}