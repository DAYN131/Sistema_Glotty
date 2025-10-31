<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Coordinador extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'coordinadores';
    
    protected $fillable = [
        'rfc_coordinador', 'nombre_coordinador', 'apellidos_coordinador',
        'num_telefono', 'correo_coordinador', 'contraseña'
    ];

    protected $hidden = ['contraseña', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->contraseña;
    }
}