<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceItemsTableSeeder extends Seeder
{
    public function run()
    {
        $invoiceItems = [];
        $invoices = DB::table('invoices')->pluck('id');
        $services = DB::table('medical_services')->pluck('id');
        $inventoryItems = DB::table('inventory_items')->pluck('id');
        
        foreach ($invoices as $invoiceId) {
            $itemsCount = rand(1, 5);
            
            for ($i = 0; $i < $itemsCount; $i++) {
                $isService = rand(0, 1);
                $quantity = rand(1, 3);
                
                if ($isService) {
                    $service = DB::table('medical_services')
                        ->where('id', $services[rand(0, count($services) - 1)])
                        ->first();
                    
                    $unitPrice = $service->price;
                    $description = $service->name;
                    $serviceId = $service->id;
                    $inventoryItemId = null;
                } else {
                    $item = DB::table('inventory_items')
                        ->where('id', $inventoryItems[rand(0, count($inventoryItems) - 1)])
                        ->first();
                    
                    $unitPrice = $item->unit_price;
                    $description = $item->name;
                    $serviceId = null;
                    $inventoryItemId = $item->id;
                }
                
                $invoiceItems[] = [
                    'id' => Str::uuid(),
                    'invoice_id' => $invoiceId,
                    'service_id' => $serviceId,
                    'inventory_item_id' => $inventoryItemId,
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $quantity * $unitPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('invoice_items')->insert($invoiceItems);
    }
}