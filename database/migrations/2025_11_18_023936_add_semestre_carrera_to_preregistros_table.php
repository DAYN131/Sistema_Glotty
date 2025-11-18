<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('preregistros', function (Blueprint $table) {
            $table->string('semestre_carrera', 50)->nullable()->after('horario_preferido_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('preregistros', function (Blueprint $table) {
            $table->dropColumn('semestre_carrera');
        });
    }
};