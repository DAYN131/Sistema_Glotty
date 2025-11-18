<?php
// database/migrations/2025_11_18_010000_fix_preregistros_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // PASO 1: Agregar campo faltante (esto siempre es seguro)
        Schema::table('preregistros', function (Blueprint $table) {
            if (!Schema::hasColumn('preregistros', 'fecha_limite_pago')) {
                $table->date('fecha_limite_pago')->nullable()->after('pagado');
            }
        });

        // PASO 2: Renombrar columnas SIN eliminar FKs (más seguro)
        if (Schema::hasColumn('preregistros', 'horario_solicitado_id') && 
            !Schema::hasColumn('preregistros', 'horario_preferido_id')) {
            Schema::table('preregistros', function (Blueprint $table) {
                $table->renameColumn('horario_solicitado_id', 'horario_preferido_id');
            });
        }

        if (Schema::hasColumn('preregistros', 'pagado') && 
            !Schema::hasColumn('preregistros', 'pago_estado')) {
            Schema::table('preregistros', function (Blueprint $table) {
                $table->renameColumn('pagado', 'pago_estado');
            });
        }

    
    }

    public function down()
    {
        // Revertir cambios de manera segura
        Schema::table('preregistros', function (Blueprint $table) {
            if (Schema::hasColumn('preregistros', 'fecha_limite_pago')) {
                $table->dropColumn('fecha_limite_pago');
            }
            
            if (Schema::hasColumn('preregistros', 'horario_preferido_id')) {
                $table->renameColumn('horario_preferido_id', 'horario_solicitado_id');
            }
            
            if (Schema::hasColumn('preregistros', 'pago_estado')) {
                $table->renameColumn('pago_estado', 'pagado');
            }
        });
    }
};