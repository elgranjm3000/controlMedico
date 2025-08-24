<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MedicalServiceController extends Controller
{
    /**
     * Display a listing of medical services.
     */
    public function index(Request $request)
    {
        $query = MedicalService::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
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

        // Filter by price range
        if ($request->has('price_min') && !empty($request->price_min)) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && !empty($request->price_max)) {
            $query->where('price', '<=', $request->price_max);
        }

        // Order by name
        $query->orderBy('name', 'asc');

        $medicalServices = $query->paginate(20)->appends($request->all());
        
        // Get summary statistics
        $totalServices = MedicalService::where('is_active', true)->count();
        $averagePrice = MedicalService::where('is_active', true)->avg('price') ?? 0;
        $totalRevenue = \App\Models\InvoiceItem::whereHas('invoice', function($q) {
            $q->where('status', 'pagada');
        })->whereNotNull('service_id')->sum('total');
        
        return view('medical-services.index', compact(
            'medicalServices', 
            'totalServices', 
            'averagePrice', 
            'totalRevenue'
        ));
    }

    /**
     * Show the form for creating a new medical service.
     */
    public function create()
    {
        return view('medical-services.create');
    }

    /**
     * Store a newly created medical service in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:medical_services,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:consulta,estudio,procedimiento,laboratorio',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MedicalService::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'is_active' => true,
        ]);

        return redirect()->route('medical-services.index')
            ->with('success', 'Servicio médico creado exitosamente.');
    }

    /**
     * Display the specified medical service.
     */
    public function show(MedicalService $medicalService)
    {
        // Get usage statistics
        $totalUsed = \App\Models\InvoiceItem::where('service_id', $medicalService->id)
            ->whereHas('invoice', function($q) {
                $q->where('status', 'pagada');
            })
            ->sum('quantity');
            
        $totalRevenue = \App\Models\InvoiceItem::where('service_id', $medicalService->id)
            ->whereHas('invoice', function($q) {
                $q->where('status', 'pagada');
            })
            ->sum('total');
        
        // Get recent usage
        $recentUsage = \App\Models\InvoiceItem::with(['invoice.patient'])
            ->where('service_id', $medicalService->id)
            ->whereHas('invoice')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('medical-services.show', compact(
            'medicalService', 
            'totalUsed', 
            'totalRevenue', 
            'recentUsage'
        ));
    }

    /**
     * Show the form for editing the specified medical service.
     */
    public function edit(MedicalService $medicalService)
    {
        return view('medical-services.edit', compact('medicalService'));
    }

    /**
     * Update the specified medical service in storage.
     */
    public function update(Request $request, MedicalService $medicalService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:medical_services,name,' . $medicalService->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:consulta,estudio,procedimiento,laboratorio',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $medicalService->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('medical-services.index')
            ->with('success', 'Servicio médico actualizado exitosamente.');
    }

    /**
     * Remove the specified medical service from storage.
     */
    public function destroy(MedicalService $medicalService)
    {
        try {
            // Check if service has been used in invoices
            $usedInInvoices = \App\Models\InvoiceItem::where('service_id', $medicalService->id)->exists();
            
            if ($usedInInvoices) {
                return redirect()->route('medical-services.index')
                    ->with('error', 'No se puede desactivar el servicio. Ha sido utilizado en facturas.');
            }
            
            // Soft delete - deactivate instead of permanent deletion
            $medicalService->update(['is_active' => false]);
            
            return redirect()->route('medical-services.index')
                ->with('success', 'Servicio médico desactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('medical-services.index')
                ->with('error', 'Error al desactivar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Activate a deactivated medical service.
     */
    public function activate(MedicalService $medicalService)
    {
        $medicalService->update(['is_active' => true]);
        
        return redirect()->route('medical-services.index')
            ->with('success', 'Servicio médico activado exitosamente.');
    }

    /**
     * Search medical services for autocomplete.
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $services = MedicalService::where('is_active', true)
            ->where(function($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('description', 'LIKE', "%{$term}%");
            })
            ->limit(15)
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'text' => $service->name . ' - $' . number_format($service->price, 2),
                    'name' => $service->name,
                    'price' => $service->price,
                    'category' => $service->category,
                    'description' => $service->description,
                ];
            });

        return response()->json($services);
    }

    /**
     * Get service statistics.
     */
    public function getStats()
    {
        $stats = [
            'total_services' => MedicalService::where('is_active', true)->count(),
            'by_category' => MedicalService::where('is_active', true)
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category'),
            'average_price' => MedicalService::where('is_active', true)->avg('price'),
            'price_range' => [
                'min' => MedicalService::where('is_active', true)->min('price'),
                'max' => MedicalService::where('is_active', true)->max('price'),
            ],
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Bulk update prices.
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'update_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:medical_services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updatedServices = [];
            $services = MedicalService::whereIn('id', $request->service_ids)->get();

            foreach ($services as $service) {
                $oldPrice = $service->price;
                
                if ($request->update_type === 'percentage') {
                    $newPrice = $oldPrice * (1 + ($request->value / 100));
                } else {
                    $newPrice = $oldPrice + $request->value;
                }
                
                // Ensure price is not negative
                $newPrice = max(0, $newPrice);
                
                $service->update(['price' => $newPrice]);
                
                $updatedServices[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'change' => $newPrice - $oldPrice
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($updatedServices) . ' servicios actualizados exitosamente.',
                'updated_services' => $updatedServices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en actualización masiva: ' . $e->getMessage()
            ], 500);
        }
    }
}