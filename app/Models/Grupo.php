<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    // QUITA SoftDeletes - la tabla no tiene deleted_at
    // use SoftDeletes;

    protected $table = 'grupos';
    
    protected $fillable = [
        'nivel_ingles',
        'letra_grupo', 
        'periodo_id',
        'horario_id',
        'aula_id',
        'profesor_id',
        'capacidad_maxima',
        'estudiantes_inscritos',
        'estado'
    ];

    // Relación con profesor
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id', 'id_profesor');
    }

    // Relación con aula
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'id_aula');
    }

    // Relación con horario
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    // Relación con periodo
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    // Relación con preregistros
    public function preregistros()
    {
        return $this->hasMany(Preregistro::class, 'grupo_asignado_id');
    }

    // Método auxiliar para verificar si tiene estudiantes
    public function tieneEstudiantes(): bool
    {
        return $this->estudiantes_inscritos > 0 || $this->preregistros()->exists();
    }
}