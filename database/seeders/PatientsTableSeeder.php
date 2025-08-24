<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientsTableSeeder extends Seeder
{
    public function run()
    {
        $patients = [];
        $firstNames = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Laura', 'Pedro', 'Sofía', 'José', 'Elena'];
        $lastNames = ['García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Pérez', 'Sánchez', 'Ramírez', 'Torres'];
        
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            $patients[] = [
                'id' => Str::uuid(),
                'name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@example.com'),
                'phone' => '7' . rand(1000000, 9999999),
                'rfc_nit' => strtoupper(Str::random(10)),
                'address' => 'Calle ' . rand(1, 100) . ', #' . rand(100, 500) . ', Col. Centro',
                'birth_date' => Carbon::now()->subYears(rand(18, 80))->subDays(rand(0, 365)),
                'gender' => ['masculino', 'femenino', 'otro'][rand(0, 2)],
                'medical_notes' => $i % 4 == 0 ? 'Paciente con alergia a ' . ['penicilina', 'aspirina', 'mariscos', 'polvo'][rand(0, 3)] : null,
                'is_active' => rand(0, 10) > 1, // 90% activos
                'created_at' => Carbon::now()->subDays(rand(0, 365)),
                'updated_at' => Carbon::now()->subDays(rand(0, 365)),
            ];
        }

        DB::table('patients')->insert($patients);
    }
}