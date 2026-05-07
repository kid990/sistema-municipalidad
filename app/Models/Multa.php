<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    protected $table = 'multas';

    public $timestamps = false;

    protected $fillable = [
        'comunero_id',
        'monto',
        'motivo',
        'fecha_emision',
        'estado_pago',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_emision' => 'date',
        'estado_pago' => 'boolean',
    ];

    public function comunero()
    {
        return $this->belongsTo(Comunero::class);
    }
}
