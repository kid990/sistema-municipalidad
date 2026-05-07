<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaFaena extends Model
{
    protected $table = 'asistencia_faenas';
    public $timestamps = false;

    protected $fillable = [
        'fecha_faena_id',
        'comunero_id',
        'estado_asistencia',
    ];

    public function fechaFaena()
    {
        return $this->belongsTo(FechaFaena::class);
    }

    public function comunero()
    {
        return $this->belongsTo(Comunero::class);
    }
}
