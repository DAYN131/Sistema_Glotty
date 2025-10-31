<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('correo_personal')->unique();
            $table->string('nombre_completo');
            $table->string('numero_telefonico')->nullable();
            $table->enum('genero', ['M', 'F', 'Otro'])->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo_usuario', ['interno', 'externo']);
            $table->string('correo_institucional')->unique()->nullable();
            $table->string('numero_control')->unique()->nullable();
            $table->string('especialidad')->nullable();
            $table->string('contraseña'); // Hash bcrypt
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};