<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Filter by stock level
        if ($request->has('stock_level') && !empty($request->stock_level)) {
            switch ($request->stock_level) {
                case 'low':
                    $query->whereRaw('current_stock <= minimum_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'good':
                    $query->whereRaw('current_stock > minimum_stock');
                    break;
            }
        }

        // Order by name
        $query->orderBy('name', 'asc');

        $inventoryItems = $query->paginate(20)->appends($request->all());
        
        // Get summary statistics
        $totalItems = InventoryItem::where('is_active', true)->count();
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)->count();
        $outOfStockItems = InventoryItem::where('current_stock', 0)
            ->where('is_active', true)->count();
        $totalValue = InventoryItem::where('is_active', true)
            ->selectRaw('SUM(current_stock * unit_price) as total')
            ->value('total') ?? 0;
        
        return view('inventory-items.index', compact(
            'inventoryItems', 
            'totalItems', 
            'lowStockItems', 
            'outOfStockItems', 
            'totalValue'
        ));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        // Generate next code
        $lastItem = InventoryItem::orderBy('created_at', 'desc')->first();
        $nextCode = $this->generateItemCode($lastItem);

        return view('inventory-items.create', compact('nextCode'));
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:50|unique:inventory_items,code',
            'category' => 'required|in:medicamento,insumo_medico,material_oficina,equipo',
            'unit_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_measure' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item = InventoryItem::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'code' => strtoupper($request->code),
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'current_stock' => $request->current_stock,
            'minimum_stock' => $request->minimum_stock,
            'unit_measure' => $request->unit_measure,
            'is_active' => true,
        ]);

        // Create initial inventory movement if stock > 0
        if ($request->current_stock > 0) {
            InventoryMovement::create([
                'id' => Str::uuid(),
                'inventory_item_id' => $item->id,
                'type' => 'entrada',
                'quantity' => $request->current_stock,
                'unit_price' => $request->unit_price,
                'reason' => 'inventario_inicial',
                'notes' => 'Stock inicial del producto',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory-items.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->load(['movements.user']);
        
        // Get movement statistics
        $totalEntered = $inventoryItem->movements()
            ->where('type', 'entrada')
            ->sum('quantity');
            
        $totalExited = $inventoryItem->movements()
            ->where('type', 'salida')
            ->sum('quantity');
        
        // Get recent movements
        $recentMovements = $inventoryItem->movements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('inventory-items.show', compact(
            'inventoryItem', 
            'totalEntered', 
            'totalExited', 
            'recentMovements'
        ));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        return view('inventory-items.edit', compact('inventoryItem'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:50|unique:inventory_items,code,' . $inventoryItem->id,
            'category' => 'required|in:medicamento,insumo_medico,material_oficina,equipo',
            'unit_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_measure' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $inventoryItem->update([
            'name' => $request->name,
            'description' => $request->description,
            'code' => strtoupper($request->code),
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'minimum_stock' => $request->minimum_stock,
            'unit_measure' => $request->unit_measure,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('inventory-items.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            // Check if item has been used in invoices
            $usedInInvoices = \App\Models\InvoiceItem::where('inventory_item_id', $inventoryItem->id)->exists();
            
            if ($usedInInvoices) {
                return redirect()->route('inventory-items.index')
                    ->with('error', 'No se puede desactivar el producto. Ha sido utilizado en facturas.');
            }
            
            // Soft delete - deactivate instead of permanent deletion
            $inventoryItem->update(['is_active' => false]);
            
            return redirect()->route('inventory-items.index')
                ->with('success', 'Producto desactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('inventory-items.index')
                ->with('error', 'Error al desactivar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Activate a deactivated inventory item.
     */
    public function activate(InventoryItem $inventoryItem)
    {
        $inventoryItem->update(['is_active' => true]);
        
        return redirect()->route('inventory-items.index')
            ->with('success', 'Producto activado exitosamente.');
    }

    /**
     * Adjust inventory stock.
     */
    public function adjustStock(Request $request, InventoryItem $inventoryItem)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:entrada,salida',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|in:compra,venta,consumo,ajuste,perdida,caducidad',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if we have enough stock for output
        if ($request->type === 'salida' && $inventoryItem->current_stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Stock actual: ' . $inventoryItem->current_stock
            ], 422);
        }

        try {
            // Create movement
            InventoryMovement::create([
                'id' => Str::uuid(),
                'inventory_item_id' => $inventoryItem->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            // Update stock
            if ($request->type === 'entrada') {
                $inventoryItem->increment('current_stock', $request->quantity);
            } else {
                $inventoryItem->decrement('current_stock', $request->quantity);
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente.',
                'new_stock' => $inventoryItem->fresh()->current_stock
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock items.
     */
    public function getLowStock()
    {
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'current_stock' => $item->current_stock,
                    'minimum_stock' => $item->minimum_stock,
                    'unit_measure' => $item->unit_measure,
                    'category' => ucfirst(str_replace('_', ' ', $item->category)),
                    'is_critical' => $item->current_stock <= ($item->minimum_stock * 0.5),
                    'is_out_of_stock' => $item->current_stock == 0,
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $lowStockItems
        ]);
    }

    /**
     * Search inventory items for autocomplete.
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $includeOutOfStock = $request->boolean('include_out_of_stock', false);
        
        $query = InventoryItem::where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('code', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });

        if (!$includeOutOfStock) {
            $query->where('current_stock', '>', 0);
        }

        $items = $query->limit(15)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name . ' (' . $item->code . ')',
                    'name' => $item->name,
                    'code' => $item->code,
                    'current_stock' => $item->current_stock,
                    'unit_price' => $item->unit_price,
                    'unit_measure' => $item->unit_measure,
                    'category' => $item->category,
                    'is_low_stock' => $item->current_stock <= $item->minimum_stock,
                ];
            });

        return response()->json($items);
    }

    /**
     * Generate item code.
     */
    private function generateItemCode($lastItem = null)
    {
        $year = now()->format('Y');
        
        if ($lastItem) {
            // Extract number from last code
            $parts = explode('-', $lastItem->code);
            if (count($parts) >= 2) {
                $lastNumber = (int) $parts[1];
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('PROD-%04d', $nextNumber);
    }

    /**
     * Bulk update stock levels.
     */
    public function bulkUpdateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|uuid|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updatedItems = [];

            foreach ($request->items as $itemData) {
                $item = InventoryItem::find($itemData['id']);
                if ($item) {
                    $oldStock = $item->current_stock;
                    $newStock = (int) $itemData['quantity'];
                    $difference = $newStock - $oldStock;

                    if ($difference != 0) {
                        // Create movement
                        InventoryMovement::create([
                            'id' => Str::uuid(),
                            'inventory_item_id' => $item->id,
                            'type' => $difference > 0 ? 'entrada' : 'salida',
                            'quantity' => abs($difference),
                            'unit_price' => $item->unit_price,
                            'reason' => 'ajuste',
                            'notes' => 'Ajuste masivo de inventario',
                            'user_id' => auth()->id(),
                        ]);

                        // Update stock
                        $item->update(['current_stock' => $newStock]);
                        
                        $updatedItems[] = [
                            'id' => $item->id,
                            'name' => $item->name,
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock,
                            'difference' => $difference
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($updatedItems) . ' productos actualizados exitosamente.',
                'updated_items' => $updatedItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en actualización masiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export inventory data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = InventoryItem::where('is_active', true);
        
        // Apply filters
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('stock_level') && !empty($request->stock_level)) {
            switch ($request->stock_level) {
                case 'low':
                    $query->whereRaw('current_stock <= minimum_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
            }
        }
        
        $items = $query->orderBy('name')->get();
        
        if ($format === 'csv') {
            $filename = 'inventario_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function() use ($items) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Código', 'Nombre', 'Categoría', 'Stock Actual', 
                    'Stock Mínimo', 'Precio Unitario', 'Unidad de Medida', 'Estado'
                ]);
                
                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->code,
                        $item->name,
                        ucfirst(str_replace('_', ' ', $item->category)),
                        $item->current_stock,
                        $item->minimum_stock,
                        $item->unit_price,
                        $item->unit_measure,
                        $item->current_stock <= $item->minimum_stock ? 'Stock Bajo' : 'OK'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        // Default JSON response
        return response()->json([
            'success' => true,
            'items' => $items->map(function($item) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                    'category' => ucfirst(str_replace('_', ' ', $item->category)),
                    'current_stock' => $item->current_stock,
                    'minimum_stock' => $item->minimum_stock,
                    'unit_price' => $item->unit_price,
                    'unit_measure' => $item->unit_measure,
                    'total_value' => $item->current_stock * $item->unit_price,
                    'is_low_stock' => $item->current_stock <= $item->minimum_stock,
                ];
            })
        ]);
    }
}