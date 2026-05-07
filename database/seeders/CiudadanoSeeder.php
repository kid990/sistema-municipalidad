<?php

namespace Database\Seeders;

use App\Models\Ciudadano;
use App\Models\Comunero;
use Illuminate\Database\Seeder;

class CiudadanoSeeder extends Seeder
{
    public function run(): void
    {
        $ciudadanos = [
            [
                'dni' => '12345678',
                'nombres' => 'Juan Carlos',
                'ape_paterno' => 'Quispe',
                'ape_materno' => 'Huaman',
                'fecha_nacimiento' => '1985-04-12',
                'genero' => 'M',
                'email' => 'juan.quispe@example.com',
                'telefono' => '987654321',
                'direccion_referencia' => 'Frente a la plaza principal',
            ],
            [
                'dni' => '23456789',
                'nombres' => 'Maria Elena',
                'ape_paterno' => 'Condori',
                'ape_materno' => 'Mamani',
                'fecha_nacimiento' => '1990-09-23',
                'genero' => 'F',
                'email' => 'maria.condori@example.com',
                'telefono' => '976543210',
                'direccion_referencia' => 'A dos cuadras del local comunal',
            ],
            [
                'dni' => '34567890',
                'nombres' => 'Luis Alberto',
                'ape_paterno' => 'Flores',
                'ape_materno' => 'Ramos',
                'fecha_nacimiento' => '1978-01-30',
                'genero' => 'M',
                'email' => 'luis.flores@example.com',
                'telefono' => '965432109',
                'direccion_referencia' => 'Junto al campo deportivo',
            ],
        ];

        foreach ($ciudadanos as $ciudadano) {
            $ciudadanoObj = Ciudadano::updateOrCreate(
                ['dni' => $ciudadano['dni']],
                $ciudadano
            );

            // Crear registro de comunero si no existe
            Comunero::firstOrCreate(
                ['ciudadano_id' => $ciudadanoObj->id],
                [
                    'estado_comunero' => 'Activo',
                    'fecha_empadronamiento' => now()->format('Y-m-d')
                ]
            );
        }

        // Crear ciudadanos adicionales con factory
        Ciudadano::factory()->count(12)->create()->each(function ($ciudadano) {
            Comunero::firstOrCreate(
                ['ciudadano_id' => $ciudadano->id],
                [
                    'estado_comunero' => 'Activo',
                    'fecha_empadronamiento' => now()->format('Y-m-d')
                ]
            );
        });
    }
}
