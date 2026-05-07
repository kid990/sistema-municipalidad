<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CiudadanoCargo extends Model
{
    protected $table = 'ciudadano_cargo';

    protected $fillable = [
        'gestion_id',
        'cargo_id',
        'ciudadano_id',
        'fecha_inicio',
        'fecha_fin',
        'estado_asignacion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function ciudadano()
    {
        return $this->belongsTo(Ciudadano::class);
    }

    public function getActivoAttribute(): bool
    {
        return $this->estado_asignacion === 'Vigente';
    }
}
