<?php
// database/migrations/2025_11_13_xxxxxx_add_timestamps_to_periodos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToPeriodosTable extends Migration
{
    public function up()
    {
     Schema::table('periodos', function (Blueprint $table) {
    if (!Schema::hasColumn('periodos', 'updated_at')) {
        $table->timestamp('updated_at')->nullable();
    }
});


    }

    public function down()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
