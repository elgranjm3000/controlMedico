<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConsultationRoomsTableSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'id' => Str::uuid(),
                'name' => 'Consulta 1',
                'location' => 'Planta Baja, Ala Este',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Consulta 2',
                'location' => 'Planta Baja, Ala Oeste',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Consulta 3',
                'location' => 'Primer Piso, Ala Norte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Urgencias 1',
                'location' => 'Planta Baja, Área de Urgencias',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Quirófano Minor',
                'location' => 'Primer Piso, Área Quirúrgica',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('consultation_rooms')->insert($rooms);
    }
}