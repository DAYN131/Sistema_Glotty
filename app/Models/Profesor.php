<?php
// app/Models/Profesor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Profesor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';
    
    // ✅ QUITAR completamente estas líneas
    // public $incrementing = false;
    // protected $keyType = 'string';
    
    protected $fillable = [
        'rfc_profesor', 'nombre_profesor', 'apellidos_profesor',
        'num_telefono', 'correo_profesor', 'contraseña'
    ];

    protected $hidden = ['contraseña', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // Relación con grupos
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'profesor_id', 'id_profesor');
    }
}