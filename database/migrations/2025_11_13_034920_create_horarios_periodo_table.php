<?php
// database/migrations/2024_01_01_000007_create_horarios_periodo_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosPeriodoTable extends Migration
{
    public function up()
    {
        Schema::create('horarios_periodo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->foreignId('horario_base_id')->constrained('horarios')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['periodo_id', 'horario_base_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios_periodo');
    }
}