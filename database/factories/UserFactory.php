<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */

use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Coordinador;
use Exception;


class UserFactory
{
    public static function createUser($type, $data)
    {
        switch ($type) {
            case 'alumno':
                return Alumno::create([
                    'no_control' => $data['no_control'],
                    'nombre_alumno' => $data['nombre_alumno'],
                    'apellidos_alumno' => $data['apellidos_alumno'],
                    'carrera' => $data['carrera'],
                    'correo_institucional' => $data['correo_institucional'],
                    'contraseña' => bcrypt($data['password']),
                ]);
            case 'profesor':
                return Profesor::create([
                    'rfc_profesor' => $data['rfc_profesor'],
                    'nombre_profesor' => $data['nombre_profesor'],
                    'apellidos_profesor' => $data['apellidos_profesor'],
                    'correo_profesor' => $data['correo_profesor'],
                    'num_telefono' => $data['num_telefono'],
                    'contraseña' => bcrypt($data['password']),
                ]);
            case 'coordinador':
                return Coordinador::create([
                    'rfc_coordinador' => $data['rfc_coordinador'],
                    'nombre_coordinador' => $data['nombre_coordinador'],
                    'apellidos_coordinador' => $data['apellidos_coordinador'],
                    'correo_coordinador' => $data['correo_coordinador'],
                    'num_telefono' => $data['num_telefono'],
                    'contraseña' => bcrypt($data['password']),
                ]);
            default:
                throw new Exception("Tipo de usuario no válido");
        }
    }
}