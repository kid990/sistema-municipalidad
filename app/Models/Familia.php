<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    protected $table = 'familias';

    public $timestamps = false;

    protected $fillable = [
        'nombre_familia',
        'lote_id',
        'jefe_familia_id',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function jefeFamilia()
    {
        return $this->belongsTo(Ciudadano::class, 'jefe_familia_id');
    }
}
