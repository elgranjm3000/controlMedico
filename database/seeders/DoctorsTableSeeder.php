<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DoctorsTableSeeder extends Seeder
{
    public function run()
    {
        $doctors = [
            [
                'id' => Str::uuid(),
                'name' => 'Dr. Alejandro',
                'last_name' => 'Martínez',
                'specialty' => 'Cardiología',
                'license_number' => 'CRM-' . rand(10000, 99999),
                'email' => 'alejandro.martinez@clinica.com',
                'phone' => '7' . rand(1000000, 9999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dra. Gabriela',
                'last_name' => 'López',
                'specialty' => 'Pediatría',
                'license_number' => 'CRM-' . rand(10000, 99999),
                'email' => 'gabriela.lopez@clinica.com',
                'phone' => '7' . rand(1000000, 9999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dr. Ricardo',
                'last_name' => 'Fernández',
                'specialty' => 'Ortopedia',
                'license_number' => 'CRM-' . rand(10000, 99999),
                'email' => 'ricardo.fernandez@clinica.com',
                'phone' => '7' . rand(1000000, 9999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dra. Carolina',
                'last_name' => 'Ramírez',
                'specialty' => 'Dermatología',
                'license_number' => 'CRM-' . rand(10000, 99999),
                'email' => 'carolina.ramirez@clinica.com',
                'phone' => '7' . rand(1000000, 9999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Dr. Miguel',
                'last_name' => 'Sánchez',
                'specialty' => 'Ginecología',
                'license_number' => 'CRM-' . rand(10000, 99999),
                'email' => 'miguel.sanchez@clinica.com',
                'phone' => '7' . rand(1000000, 9999999),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('doctors')->insert($doctors);
    }
}