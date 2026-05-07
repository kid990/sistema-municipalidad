<?php

namespace Database\Factories;

use App\Models\Ciudadano;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ciudadano>
 */
class CiudadanoFactory extends Factory
{
    protected $model = Ciudadano::class;

    public function definition(): array
    {
        return [
            'dni' => fake()->unique()->numerify('########'),
            'nombres' => fake()->firstName().' '.fake()->firstName(),
            'ape_paterno' => fake()->lastName(),
            'ape_materno' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-75 years', '-18 years')->format('Y-m-d'),
            'genero' => fake()->randomElement(['M', 'F', 'Otro']),
            'email' => fake()->boolean(80) ? fake()->unique()->safeEmail() : null,
            'telefono' => fake()->optional()->numerify('9########'),
            'direccion_referencia' => fake()->optional()->sentence(8),
        ];
    }
}
