<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
    
class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador General',
            'email' => 'admin@clinica.com',
            'password' => Hash::make('password123'),
            'role' => 'administrador',
        ]);

        User::create([
            'name' => 'María González',
            'email' => 'contador@clinica.com',
            'password' => Hash::make('password123'),
            'role' => 'contador',
        ]);

        User::create([
            'name' => 'Ana Recepción',
            'email' => 'recepcion@clinica.com',
            'password' => Hash::make('password123'),
            'role' => 'recepcionista',
        ]);
    }
}