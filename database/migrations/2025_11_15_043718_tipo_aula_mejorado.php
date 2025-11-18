<?php
// database/migrations/2025_11_15_043718_tipo_aula_mejorado.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Para PostgreSQL, agregamos una CHECK constraint
        DB::statement("
            ALTER TABLE aulas 
            ADD CONSTRAINT aulas_tipo_check 
            CHECK (tipo IN (
                'regular', 
                'laboratorio', 
                'computo', 
                'audiovisual'
            ))
        ");
        
    }

    public function down()
    {
        // Eliminar la constraint al revertir
        DB::statement("ALTER TABLE aulas DROP CONSTRAINT IF EXISTS aulas_tipo_check");
        DB::statement("ALTER TABLE aulas ALTER COLUMN tipo DROP DEFAULT");
    }
};