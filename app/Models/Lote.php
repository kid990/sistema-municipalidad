<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';

    public $timestamps = false;

    protected $fillable = [
        'numero_lote',
        'manzana',
        'area_m2',
        'referencia_ubicacion',
        'estado',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
    ];

    public function familias()
    {
        return $this->hasMany(Familia::class);
    }
}
