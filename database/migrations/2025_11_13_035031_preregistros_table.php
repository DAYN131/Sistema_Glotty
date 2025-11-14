<?php
// database/migrations/2024_01_01_000009_create_preregistros_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preregistros', function (Blueprint $table) {
            $table->id();
            
            // Relación con usuario (YA TIENE toda la info personal)
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            
            // Periodo y horario solicitado
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->integer('nivel_solicitado'); // 1,2,3,4,5
            $table->foreignId('horario_preferido_id')->constrained('horarios_periodo')->onDelete('cascade');
            
            // Información académica ACTUAL (puede cambiar cada periodo)
            $table->string('semestre_actual', 50)->nullable(); // Ej: "4to Semestre"
            $table->string('carrera_actual', 100)->nullable(); // Ej: "Ingeniería en Sistemas"
            
            // Asignación y estado
            $table->foreignId('grupo_asignado_id')->nullable()->constrained('grupos')->onDelete('set null');
            $table->enum('estado', ['pendiente', 'asignado', 'cursando', 'finalizado', 'cancelado'])->default('pendiente');
            $table->enum('pago_estado', ['pendiente', 'pagado', 'rechazado'])->default('pendiente');
            $table->date('fecha_limite_pago')->nullable();
            
            // Campos de auditoría
            $table->timestamps();
            
            // Índices únicos
            $table->unique(['usuario_id', 'periodo_id']); // Un pre-registro por usuario por periodo
        });
    }

    public function down()
    {
        Schema::dropIfExists('preregistros');
    }
};