<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ciudadano extends Model
{
    use HasFactory;

    protected $table = 'ciudadanos';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'nombres',
        'ape_paterno',
        'ape_materno',
        'fecha_nacimiento',
        'genero',
        'email',
        'telefono',
        'direccion_referencia',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'string',
    ];

    public function comunero(): HasOne
    {
        return $this->hasOne(Comunero::class);
    }

    public function familiasComoJefe(): HasMany
    {
        return $this->hasMany(Familia::class, 'jefe_familia_id');
    }

    public function ciudadanoCargos(): HasMany
    {
        return $this->hasMany(CiudadanoCargo::class);
    }

    public function getApellidoPAttribute(): ?string
    {
        return $this->ape_paterno;
    }

    public function getApellidoMAttribute(): ?string
    {
        return $this->ape_materno;
    }

    public function getFechaNAttribute()
    {
        return $this->fecha_nacimiento;
    }

    public function getActivoAttribute(): bool
    {
        return $this->comunero?->estado_comunero === 'Activo';
    }
}
