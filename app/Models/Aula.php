<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $primaryKey = 'id_aula';
    public $incrementing = false;
    protected $keyType = 'string';

    const TIPO_AULA = [
        'regular' => 'Regular',
        'laboratorio' => 'Laboratorio'
    ];

    protected $fillable = [
        'id_aula',
        'edificio',
        'nombre_aula', // Cambiar numero_aula por nombre_aula
        'capacidad', 
        'tipo_aula'
    ];

    // Relación con grupos
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'aula_id', 'id_aula');
    }

    // Hook para generar id_aula automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aula) {
            if (empty($aula->id_aula)) {
                $aula->id_aula = strtoupper(trim($aula->edificio)) . '-' . trim($aula->nombre_aula);
            }
        });

        static::updating(function ($aula) {
            // Si cambia el edificio o nombre, actualizar id_aula
            if ($aula->isDirty(['edificio', 'nombre_aula'])) {
                $nuevo_id = strtoupper(trim($aula->edificio)) . '-' . trim($aula->nombre_aula);
                
                // Verificar que no exista otro aula con el mismo ID
                if ($nuevo_id !== $aula->getOriginal('id_aula') && 
                    self::where('id_aula', $nuevo_id)->exists()) {
                    return false; // Prevenir la actualización
                }
                
                $aula->id_aula = $nuevo_id;
            }
        });
    }
}