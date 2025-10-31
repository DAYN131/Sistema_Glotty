<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profesores', function (Blueprint $table) {
            $table->string('id_profesor')->primary();
            $table->string('rfc_profesor');
            $table->string('nombre_profesor');
            $table->string('apellidos_profesor');
            $table->string('num_telefono');
            $table->string('correo_profesor');
            $table->string('contraseña'); // Hash bcrypt
            $table->timestamps();
            
            $table->unique('correo_profesor');
        });
    }

    public function down()
    {
        Schema::dropIfExists('profesores');
    }
};