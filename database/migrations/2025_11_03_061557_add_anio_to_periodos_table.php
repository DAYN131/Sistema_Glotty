<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->year('anio')->after('nombre')->default(date('Y'));
        });
    }

    public function down()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->dropColumn('anio');
        });
    }
};