<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechaFaena extends Model
{
    protected $table = 'fechas_faenas';
    public $timestamps = false;

    protected $fillable = [
        'faena_id',
        'fecha_realizacion',
    ];

    protected $casts = [
        'fecha_realizacion' => 'date',
    ];

    public function faena()
    {
        return $this->belongsTo(Faena::class);
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaFaena::class);
    }
}
