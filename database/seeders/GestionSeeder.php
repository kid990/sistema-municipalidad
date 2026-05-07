<?php

namespace Database\Seeders;

use App\Models\Gestion;
use Illuminate\Database\Seeder;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        Gestion::updateOrCreate(
            ['nombre_gestion' => 'Gestion 2026-2027'],
            [
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2027-12-31',
                'estado_gestion' => true,
            ]
        );
    }
}
