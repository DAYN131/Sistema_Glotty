<?php
// database/migrations/2024_01_01_000002_create_periodos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodosTable extends Migration
{
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_periodo', 50);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['configuracion', 'preregistros_activos', 'en_curso', 'finalizado'])->default('configuracion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('periodos');
    }
}