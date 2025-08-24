<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicalServicesTableSeeder extends Seeder
{
    public function run()
    {
        $services = [
            // Consultas
            [
                'id' => Str::uuid(),
                'name' => 'Consulta General',
                'description' => 'Consulta médica general con diagnóstico y tratamiento',
                'price' => 500.00,
                'category' => 'consulta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Consulta Especialidad',
                'description' => 'Consulta con médico especialista',
                'price' => 800.00,
                'category' => 'consulta',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Estudios
            [
                'id' => Str::uuid(),
                'name' => 'Radiografía',
                'description' => 'Radiografía simple de cualquier zona',
                'price' => 350.00,
                'category' => 'estudio',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ultrasonido',
                'description' => 'Estudio de ultrasonido diagnóstico',
                'price' => 600.00,
                'category' => 'estudio',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Procedimientos
            [
                'id' => Str::uuid(),
                'name' => 'Curaciones',
                'description' => 'Curación de heridas y aplicación de medicamentos',
                'price' => 200.00,
                'category' => 'procedimiento',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Suturas',
                'description' => 'Sutura de heridas simples',
                'price' => 450.00,
                'category' => 'procedimiento',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Laboratorio
            [
                'id' => Str::uuid(),
                'name' => 'Biometría Hemática',
                'description' => 'Análisis completo de sangre',
                'price' => 250.00,
                'category' => 'laboratorio',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Química Sanguínea',
                'description' => 'Perfil bioquímico completo',
                'price' => 400.00,
                'category' => 'laboratorio',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('medical_services')->insert($services);
    }
}