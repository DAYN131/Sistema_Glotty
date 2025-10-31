<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coordinadores', function (Blueprint $table) {
            $table->id();
            $table->string('rfc_coordinador');
            $table->string('nombre_coordinador');
            $table->string('apellidos_coordinador');
            $table->string('num_telefono');
            $table->string('correo_coordinador');
            $table->string('contraseña'); // Hash bcrypt
            $table->timestamps();
            
            $table->unique('correo_coordinador');
        });
    }

    public function down()
    {
        Schema::dropIfExists('coordinadores');
    }
};