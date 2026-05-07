<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faena extends Model
{
    protected $table = 'faenas';
    public $timestamps = false;

    protected $fillable = [
        'nombre_actividad',
        'costo_multa_inasistencia',
    ];

    protected $casts = [
        'costo_multa_inasistencia' => 'decimal:2',
    ];

    public function fechasFaenas(): HasMany
    {
        return $this->hasMany(FechaFaena::class);
    }

    public function getNombreAttribute(): ?string
    {
        return $this->nombre_actividad;
    }

    public function getCantidadDiasAttribute(): int
    {
        return $this->fechas_faenas_count ?? $this->fechasFaenas()->count();
    }
}
