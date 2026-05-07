<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestiones';

    public $timestamps = false;

    protected $fillable = [
        'nombre_gestion',
        'fecha_inicio',
        'fecha_fin',
        'estado_gestion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado_gestion' => 'boolean',
    ];

    public function ciudadanoCargos()
    {
        return $this->hasMany(CiudadanoCargo::class);
    }
}
