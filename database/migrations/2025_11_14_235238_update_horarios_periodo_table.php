<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios_periodo', function (Blueprint $table) {
            // Nuevos campos
            $table->string('nombre')->after('horario_base_id');
            $table->enum('tipo', ['semanal', 'sabatino'])->after('nombre');
            $table->json('dias')->after('tipo');
            $table->time('hora_inicio')->after('dias');
            $table->time('hora_fin')->after('hora_inicio');

            // created_at ya existe, falta agregar updated_at si no está
            if (!Schema::hasColumn('horarios_periodo', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('horarios_periodo', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'tipo', 'dias', 'hora_inicio', 'hora_fin']);

            if (Schema::hasColumn('horarios_periodo', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
