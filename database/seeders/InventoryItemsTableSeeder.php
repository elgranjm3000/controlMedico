<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryItemsTableSeeder extends Seeder
{
    public function run()
    {
        $items = [
            // Medicamentos
            [
                'id' => Str::uuid(),
                'name' => 'Paracetamol 500mg',
                'description' => 'Analgésico y antipirético',
                'code' => 'MED-001',
                'category' => 'medicamento',
                'unit_price' => 0.50,
                'current_stock' => 1000,
                'minimum_stock' => 100,
                'unit_measure' => 'tabletas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Amoxicilina 500mg',
                'description' => 'Antibiótico de amplio espectro',
                'code' => 'MED-002',
                'category' => 'medicamento',
                'unit_price' => 1.20,
                'current_stock' => 500,
                'minimum_stock' => 50,
                'unit_measure' => 'tabletas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Insumos médicos
            [
                'id' => Str::uuid(),
                'name' => 'Jeringas 5ml',
                'description' => 'Jeringas estériles desechables',
                'code' => 'INS-001',
                'category' => 'insumo_medico',
                'unit_price' => 0.30,
                'current_stock' => 2000,
                'minimum_stock' => 200,
                'unit_measure' => 'unidades',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Guantes de Latex Talla M',
                'description' => 'Guantes estériles desechables',
                'code' => 'INS-002',
                'category' => 'insumo_medico',
                'unit_price' => 0.25,
                'current_stock' => 3000,
                'minimum_stock' => 300,
                'unit_measure' => 'pares',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Material de oficina
            [
                'id' => Str::uuid(),
                'name' => 'Formato Historia Clínica',
                'description' => 'Formato para historia clínica',
                'code' => 'OFI-001',
                'category' => 'material_oficina',
                'unit_price' => 0.10,
                'current_stock' => 2000,
                'minimum_stock' => 100,
                'unit_measure' => 'unidades',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Equipo
            [
                'id' => Str::uuid(),
                'name' => 'Estetoscopio Littmann',
                'description' => 'Estetoscopio profesional',
                'code' => 'EQU-001',
                'category' => 'equipo',
                'unit_price' => 1200.00,
                'current_stock' => 10,
                'minimum_stock' => 2,
                'unit_measure' => 'unidades',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('inventory_items')->insert($items);
    }
}