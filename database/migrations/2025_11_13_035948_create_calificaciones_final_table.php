<?php
// database/migrations/2025_11_13_[FECHA_ACTUAL]_create_calificaciones_final_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionesFinalTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('calificaciones')) {
            Schema::create('calificaciones', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('preregistro_id')->constrained('preregistros')->onDelete('cascade');
                $table->decimal('calificacion_1', 3, 1)->nullable();
                $table->decimal('calificacion_2', 3, 1)->nullable();
                $table->decimal('calificacion_3', 3, 1)->nullable();
                $table->decimal('calificacion_4', 3, 1)->nullable();
                $table->decimal('calificacion_5', 3, 1)->nullable();
                $table->decimal('calificacion_6', 3, 1)->nullable();
                $table->decimal('calificacion_final', 3, 1)->nullable();
                $table->decimal('promedio', 3, 1)->nullable();
                $table->boolean('aprobado')->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('calificaciones');
    }
}