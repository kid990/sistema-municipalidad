<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunero extends Model
{
    protected $table = 'comuneros';

    public $timestamps = false;

    protected $fillable = [
        'ciudadano_id',
        'fecha_empadronamiento',
        'estado_comunero',
    ];

    protected $casts = [
        'fecha_empadronamiento' => 'date',
    ];

    public function ciudadano(): BelongsTo
    {
        return $this->belongsTo(Ciudadano::class);
    }

    public function asistenciaFaenas()
    {
        return $this->hasMany(AsistenciaFaena::class);
    }

    public function multas()
    {
        return $this->hasMany(Multa::class);
    }

    public function getActivoAttribute(): bool
    {
        return $this->estado_comunero === 'Activo';
    }
}
