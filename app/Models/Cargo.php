<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';
    public $timestamps = false;

    protected $fillable = [
        'nombre_cargo',
    ];

    public function ciudadanoCargos()
    {
        return $this->hasMany(CiudadanoCargo::class);
    }

    public function getNombreAttribute(): ?string
    {
        return $this->nombre_cargo;
    }

    public function getActivoAttribute(): bool
    {
        return true;
    }
}
