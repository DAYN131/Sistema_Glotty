<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->enum('nombre', ['AGOSTO-DIC', 'ENERO-JUNIO', 'INVIERNO', 'VERANO1', 'VERANO2']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('periodos');
    }
};