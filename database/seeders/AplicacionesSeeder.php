<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aplicacion;

class AplicacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aplicaciones = [
            ['nombre' => 'CLUBGG', 'descripcion' => 'Club GG', 'activo' => true],
            ['nombre' => 'POKERBROS', 'descripcion' => 'Poker Bros', 'activo' => true],
            ['nombre' => 'POKERMAN', 'descripcion' => 'Pokerman', 'activo' => true],
            ['nombre' => 'SUPREMA', 'descripcion' => 'Suprema', 'activo' => true],
            ['nombre' => 'BETPROLIVE', 'descripcion' => 'Betpro Live', 'activo' => true],
        ];

        foreach ($aplicaciones as $app) {
            Aplicacion::firstOrCreate(
                ['nombre' => $app['nombre']],
                $app
            );
        }
    }
}
