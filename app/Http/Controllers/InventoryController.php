<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('low_stock') && $request->low_stock) {
            $query->whereRaw('current_stock <= minimum_stock');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $items = $query->paginate($request->get('per_page', 15));

        // Datos adicionales para la vista
        $categories = InventoryItem::distinct()->pluck('category');
        $totalItems = InventoryItem::count();
        $activeItems = InventoryItem::where('is_active', true)->count();
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();

        return view('modules.inventory.index', compact(
            'items', 
            'categories', 
            'totalItems', 
            'activeItems', 
            'lowStockItems'
        ));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $categories = InventoryItem::distinct()->pluck('category');
        return view('modules.inventory.form', compact('categories'));
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:100|unique:inventory_items,code',
            'category' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_measure' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->get('is_active', true);

        try {
            DB::beginTransaction();

            $item = InventoryItem::create($validated);

            // Crear movimiento inicial si hay stock
            if ($validated['current_stock'] > 0) {
                InventoryMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => 'entrada',
                    'quantity' => $validated['current_stock'],
                    'unit_price' => $validated['unit_price'],
                    'reason' => 'INITIAL_STOCK',
                    'notes' => 'Stock inicial del producto',
                    'user_id' => Auth::id()
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.index')
                           ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem)
    {
        $movements = $inventoryItem->movements()
                                 ->with('user:id,name')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20);

        $totalMovements = $inventoryItem->movements()->count();
        $lastMovement = $inventoryItem->movements()->latest()->first();
        $isLowStock = $inventoryItem->isLowStock();

        return view('modules.inventory.show', compact(
            'inventoryItem', 
            'movements', 
            'totalMovements', 
            'lastMovement', 
            'isLowStock'
        ));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        $categories = InventoryItem::distinct()->pluck('category');
        return view('modules.inventory.form', compact('inventoryItem', 'categories'));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('inventory_items', 'code')->ignore($inventoryItem->id)
            ],
            'category' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_measure' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        try {
            $inventoryItem->update($validated);

            return redirect()->route('inventory.show', $inventoryItem)
                           ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            // Verificar si tiene movimientos o items de factura relacionados
            if ($inventoryItem->movements()->count() > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar el producto porque tiene movimientos registrados']);
            }

            if ($inventoryItem->invoiceItems()->count() > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar el producto porque está siendo usado en facturas']);
            }

            $inventoryItem->delete();

            return redirect()->route('inventory.index')
                           ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to add stock to inventory item.
     */
    public function showAddStock(InventoryItem $inventoryItem)
    {
        return view('modules.inventory.add-stock', compact('inventoryItem'));
    }

    /**
     * Add stock to inventory item.
     */
    public function addStock(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'reason' => 'required|string|in:PURCHASE,ADJUSTMENT,RETURN,PRODUCTION',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $previousStock = $inventoryItem->current_stock;

            // Crear movimiento de entrada
            InventoryMovement::create([
                'inventory_item_id' => $inventoryItem->id,
                'type' => 'entrada',
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'user_id' => Auth::id()
            ]);

            // Actualizar stock actual
            $inventoryItem->increment('current_stock', $validated['quantity']);

            // Actualizar precio unitario si es diferente
            if ($inventoryItem->unit_price != $validated['unit_price']) {
                $inventoryItem->update(['unit_price' => $validated['unit_price']]);
            }

            DB::commit();

            return redirect()->route('inventory.show', $inventoryItem)
                           ->with('success', "Stock agregado exitosamente. Stock anterior: {$previousStock}, Agregado: {$validated['quantity']}, Nuevo stock: {$inventoryItem->current_stock}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->withErrors(['error' => 'Error al agregar stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to remove stock from inventory item.
     */
    public function showRemoveStock(InventoryItem $inventoryItem)
    {
        return view('modules.inventory.remove-stock', compact('inventoryItem'));
    }

    /**
     * Remove stock from inventory item.
     */
    public function removeStock(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|in:SALE,ADJUSTMENT,DAMAGE,EXPIRED,THEFT',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validated['quantity'] > $inventoryItem->current_stock) {
            return back()->withInput()
                        ->withErrors(['quantity' => 'Cantidad insuficiente en stock. Stock actual: ' . $inventoryItem->current_stock]);
        }

        try {
            DB::beginTransaction();

            $previousStock = $inventoryItem->current_stock;

            // Crear movimiento de salida
            InventoryMovement::create([
                'inventory_item_id' => $inventoryItem->id,
                'type' => 'salida',
                'quantity' => $validated['quantity'],
                'unit_price' => $inventoryItem->unit_price,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'user_id' => Auth::id()
            ]);

            // Actualizar stock actual
            $inventoryItem->decrement('current_stock', $validated['quantity']);

            DB::commit();

            $message = "Stock removido exitosamente. Stock anterior: {$previousStock}, Removido: {$validated['quantity']}, Nuevo stock: {$inventoryItem->current_stock}";
            
            if ($inventoryItem->isLowStock()) {
                $message .= " ⚠️ ALERTA: Stock bajo";
            }

            return redirect()->route('inventory.show', $inventoryItem)
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->withErrors(['error' => 'Error al remover stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Display inventory movements for a specific item.
     */
    public function movements(InventoryItem $inventoryItem)
    {
        $movements = $inventoryItem->movements()
                                 ->with('user:id,name')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(30);

        return view('modules.inventory.movements', compact('inventoryItem', 'movements'));
    }

    /**
     * Display low stock items.
     */
    public function lowStock()
    {
        $items = InventoryItem::whereRaw('current_stock <= minimum_stock')
                            ->where('is_active', true)
                            ->orderBy('current_stock', 'asc')
                            ->paginate(20);

        $count = $items->total();

        return view('modules.inventory.low-stock', compact('items', 'count'));
    }

    /**
     * Display inventory summary report.
     */
    public function summary()
    {
        $totalItems = InventoryItem::count();
        $activeItems = InventoryItem::where('is_active', true)->count();
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
        $outOfStockItems = InventoryItem::where('current_stock', 0)->count();

        $totalValue = InventoryItem::selectRaw('SUM(current_stock * unit_price) as total')
                                 ->where('is_active', true)
                                 ->value('total') ?? 0;

        $categories = InventoryItem::selectRaw('category, COUNT(*) as count, SUM(current_stock * unit_price) as value')
                                 ->where('is_active', true)
                                 ->groupBy('category')
                                 ->get();

        $recentMovements = InventoryMovement::with(['inventoryItem:id,name,code', 'user:id,name'])
                                          ->orderBy('created_at', 'desc')
                                          ->take(10)
                                          ->get();

        return view('modules.inventory.summary', compact(
            'totalItems',
            'activeItems', 
            'lowStockItems',
            'outOfStockItems',
            'totalValue',
            'categories',
            'recentMovements'
        ));
    }

    /**
     * Toggle item active status.
     */
    public function toggleStatus(InventoryItem $inventoryItem)
    {
        $inventoryItem->update(['is_active' => !$inventoryItem->is_active]);

        $status = $inventoryItem->is_active ? 'activado' : 'desactivado';
        
        return back()->with('success', "Producto {$status} exitosamente");
    }
}