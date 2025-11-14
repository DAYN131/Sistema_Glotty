<?php
// database/migrations/2024_01_01_000003_create_aulas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAulasTable extends Migration
{
    public function up()
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->string('id_aula')->primary();
            $table->string('edificio');
            $table->integer('numero_aula');
            $table->integer('capacidad');
            $table->enum('tipo_aula', ['regular', 'laboratorio']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aulas');
    }
}