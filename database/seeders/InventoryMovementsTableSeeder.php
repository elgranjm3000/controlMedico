<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventoryMovementsTableSeeder extends Seeder
{
    public function run()
    {
        $movements = [];
        $inventoryItems = DB::table('inventory_items')->pluck('id');
        $users = DB::table('users')->pluck('id');
        $types = ['entrada', 'salida'];
        $reasons = ['compra', 'venta', 'ajuste', 'consumo', 'donación'];
        
        foreach ($inventoryItems as $itemId) {
            $item = DB::table('inventory_items')->where('id', $itemId)->first();
            
            // Entradas iniciales
            $movements[] = [
                'id' => Str::uuid(),
                'inventory_item_id' => $itemId,
                'type' => 'entrada',
                'quantity' => $item->current_stock + 100,
                'unit_price' => $item->unit_price,
                'reason' => 'compra',
                'notes' => 'Stock inicial',
                'user_id' => $users[rand(0, count($users) - 1)],
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(60),
            ];
            
            // Movimientos adicionales
            for ($i = 0; $i < rand(3, 10); $i++) {
                $type = $types[rand(0, 1)];
                $quantity = $type === 'entrada' ? rand(10, 100) : rand(1, 20);
                
                $movements[] = [
                    'id' => Str::uuid(),
                    'inventory_item_id' => $itemId,
                    'type' => $type,
                    'quantity' => $quantity,
                    'unit_price' => $item->unit_price,
                    'reason' => $reasons[rand(0, 4)],
                    'notes' => 'Movimiento de inventario',
                    'user_id' => $users[rand(0, count($users) - 1)],
                    'created_at' => Carbon::now()->subDays(rand(0, 59)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 59)),
                ];
            }
        }

        DB::table('inventory_movements')->insert($movements);
    }
}