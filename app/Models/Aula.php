<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aula extends Model
{
    // 1. Configuración de la clave primaria
    protected $primaryKey = 'id_aula';
    public $incrementing = false; // El ID no es auto-incremental
    protected $keyType = 'string'; // El ID es una cadena (Ej: "A-101")

    // 2. Definición de Constantes para ENUMs (Tipos de aula)
    const TIPO_AULA = [
        'teorica' => 'Teórica',
        'laboratorio' => 'Laboratorio',
        'auditorio' => 'Auditorio',
    ];

    // 3. Campos Asignables Masivamente (fillable)
    protected $fillable = [
        'edificio',
        'numero_aula',
        'capacidad',
        'tipo_aula',
    ];

    // 4. Casting de Atributos
    protected $casts = [
        'capacidad' => 'integer',
        // 'tipo_aula' se mantiene como string
    ];

    /**
     * Hook para generar el id_aula antes de crear el registro.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aula) {
            $edificio = strtoupper(trim($aula->edificio));
            $numero = trim($aula->numero_aula);

            // Formato de ID único: EDIFICIO-NUMERO (Ej: A-101, C-205)
            $aula->id_aula = "{$edificio}-{$numero}";

            // NOTA: La validación de unicidad de 'id_aula' debe realizarse en el Controller
            // usando una regla 'unique' para un manejo adecuado de errores.
        });

        // También regeneramos el id_aula si se actualiza edificio o numero_aula
        static::updating(function ($aula) {
            if ($aula->isDirty('edificio') || $aula->isDirty('numero_aula')) {
                $edificio = strtoupper(trim($aula->edificio));
                $numero = trim($aula->numero_aula);
                $aula->id_aula = "{$edificio}-{$numero}";
            }
        });
    }

    // 5. Relaciones

    /**
     * Un aula puede tener muchos grupos asignados (en la tabla grupos).
     */
    public function grupos(): HasMany
    {
        // Asumiendo que la tabla 'grupos' tiene una llave foránea 'aula_id'
        return $this->hasMany(Grupo::class, 'aula_id', 'id_aula');
    }
}
