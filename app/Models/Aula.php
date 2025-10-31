<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $primaryKey = 'id_aula';
    public $incrementing = false;
    protected $keyType = 'string';

// Campos rellenables
protected $fillable = [
    'edificio',
    'numero_aula', 
    'capacidad',
    'tipo_aula',
];

protected static function boot(){
    
    parent::boot();

    static::creating(function ($model) {
        $model->id_aula = strtoupper(trim($model->edificio)) . $model->numero_aula;
        
        // Validación adicional
        if (Aula::where('id_aula', $model->id_aula)->exists()) {
            throw new \Exception("El aula {$model->id_aula} ya existe");
        }
    });
}


}