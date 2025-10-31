<?php
// app/Models/Periodo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'periodos';
    
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean'
    ];

    // Scope para obtener el período activo actual
    public function scopeActivo($query)
    {
        $hoy = Carbon::today();
        return $query->where('activo', true)
                    ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
    }

    // Scope para períodos futuros (próximos)
    public function scopeFuturos($query)
    {
        $hoy = Carbon::today();
        return $query->where('fecha_inicio', '>', $hoy)
                    ->where('activo', true);
    }

    // Verificar si el período está activo
    public function estaActivo()
    {
        $hoy = Carbon::today();
        return $this->activo && 
               $hoy->between($this->fecha_inicio, $this->fecha_fin);
    }

    // Verificar si el período es futuro
    public function esFuturo()
    {
        $hoy = Carbon::today();
        return $this->fecha_inicio > $hoy;
    }
}