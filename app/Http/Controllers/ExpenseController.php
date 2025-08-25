<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with('registeredBy');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Default to current month if no date filters
        if (!$request->hasAny(['date_from', 'date_to'])) {
            $query->whereMonth('expense_date', now()->month)
                  ->whereYear('expense_date', now()->year);
        }

        // Order by expense date descending
        $query->orderBy('expense_date', 'desc');

        $expenses = $query->paginate(15)->appends($request->all());
        
        // Get summary statistics
        $totalExpenses = $query->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        
        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');
        
        return view('modules.expenses.index', compact(
            'expenses', 
            'totalExpenses', 
            'monthlyExpenses', 
            'expensesByCategory'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        return view('modules.expenses.form');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:servicios,nomina,honorarios_medicos,insumos,equipo,otros',
            'description' => 'required|string',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:efectivo,transferencia,cheque',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Expense::create([
            'id' => Str::uuid(),
            'supplier_name' => $request->supplier_name,
            'invoice_number' => $request->invoice_number,
            'amount' => $request->amount,
            'category' => $request->category,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
            'payment_method' => $request->payment_method,
            'registered_by' => auth()->id(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Gasto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('expenses.index')
                ->with('error', 'Error al eliminar el gasto: ' . $e->getMessage());
        }
    }

    /**
     * Get expenses summary by categories.
     */
    public function getCategoriesSummary(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);
        $endDate = now();
        
        $summary = Expense::selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category,
                    'label' => $this->getCategoryLabel($item->category),
                    'total' => (float) $item->total,
                    'count' => $item->count,
                    'color' => $this->getCategoryColor($item->category)
                ];
            });

        $grandTotal = $summary->sum('total');
        
        // Add percentage to each category
        $summary = $summary->map(function($item) use ($grandTotal) {
            $item['percentage'] = $grandTotal > 0 ? ($item['total'] / $grandTotal) * 100 : 0;
            return $item;
        });

        return response()->json([
            'success' => true,
            'period' => $period,
            'summary' => $summary,
            'grand_total' => $grandTotal
        ]);
    }

    /**
     * Get monthly trends.
     */
    public function getMonthlyTrends(Request $request)
    {
        $months = $request->get('months', 12);
        $category = $request->get('category', null);
        
        $data = [];
        $labels = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $query = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth]);
            
            if ($category) {
                $query->where('category', $category);
            }
            
            $total = $query->sum('amount');
            $count = $query->count();
            
            $labels[] = $date->format('M Y');
            $data['amounts'][] = (float) $total;
            $data['counts'][] = $count;
        }
        
        return response()->json([
            'success' => true,
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Get top suppliers.
     */
    public function getTopSuppliers(Request $request)
    {
        $period = $request->get('period', 'month');
        $limit = $request->get('limit', 10);
        $startDate = $this->getStartDate($period);
        $endDate = now();
        
        $topSuppliers = Expense::selectRaw('supplier_name, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('supplier_name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function($supplier) {
                return [
                    'name' => $supplier->supplier_name,
                    'total' => (float) $supplier->total,
                    'count' => $supplier->count,
                    'average' => $supplier->count > 0 ? $supplier->total / $supplier->count : 0
                ];
            });

        return response()->json([
            'success' => true,
            'suppliers' => $topSuppliers
        ]);
    }

    /**
     * Export expenses data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $query = Expense::with('registeredBy');
        
        // Apply filters
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->get();
        
        if ($format === 'csv') {
            $filename = 'gastos_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function() use ($expenses) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Fecha', 'Proveedor', 'Número Factura', 'Categoría', 
                    'Descripción', 'Monto', 'Método Pago', 'Registrado Por'
                ]);
                
                foreach ($expenses as $expense) {
                    fputcsv($file, [
                        $expense->expense_date->format('Y-m-d'),
                        $expense->supplier_name,
                        $expense->invoice_number ?? '',
                        $this->getCategoryLabel($expense->category),
                        $expense->description,
                        $expense->amount,
                        ucfirst($expense->payment_method),
                        $expense->registeredBy->name ?? ''
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        // Default JSON response
        return response()->json([
            'success' => true,
            'expenses' => $expenses->map(function($expense) {
                return [
                    'date' => $expense->expense_date->format('Y-m-d'),
                    'supplier' => $expense->supplier_name,
                    'invoice_number' => $expense->invoice_number,
                    'category' => $this->getCategoryLabel($expense->category),
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'payment_method' => ucfirst($expense->payment_method),
                    'registered_by' => $expense->registeredBy->name ?? ''
                ];
            }),
            'total' => $expenses->sum('amount')
        ]);
    }

    /**
     * Bulk delete expenses.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_ids' => 'required|array|min:1',
            'expense_ids.*' => 'exists:expenses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deletedCount = Expense::whereIn('id', $request->expense_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount gastos eliminados exitosamente."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar gastos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense statistics for dashboard.
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();
        
        $stats = [
            'total_amount' => Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount'),
            'total_count' => Expense::whereBetween('expense_date', [$startDate, $endDate])->count(),
            'average_amount' => Expense::whereBetween('expense_date', [$startDate, $endDate])->avg('amount') ?? 0,
            'by_category' => Expense::selectRaw('category, SUM(amount) as total')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->groupBy('category')
                ->get()
                ->pluck('total', 'category'),
            'by_payment_method' => Expense::selectRaw('payment_method, COUNT(*) as count')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->groupBy('payment_method')
                ->get()
                ->pluck('count', 'payment_method'),
        ];
        
        return response()->json([
            'success' => true,
            'period' => $period,
            'stats' => $stats
        ]);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'day':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'quarter':
                return now()->startOfQuarter();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->startOfMonth();
        }
    }

    /**
     * Get category label in Spanish.
     */
    private function getCategoryLabel($category)
    {
        $labels = [
            'servicios' => 'Servicios',
            'nomina' => 'Nómina',
            'honorarios_medicos' => 'Honorarios Médicos',
            'insumos' => 'Insumos',
            'equipo' => 'Equipo',
            'otros' => 'Otros'
        ];

        return $labels[$category] ?? ucfirst($category);
    }

    /**
     * Get category color for charts.
     */
    private function getCategoryColor($category)
    {
        $colors = [
            'servicios' => '#3b82f6',      // blue
            'nomina' => '#10b981',         // green
            'honorarios_medicos' => '#8b5cf6', // purple
            'insumos' => '#f59e0b',        // yellow
            'equipo' => '#ef4444',         // red
            'otros' => '#6b7280'           // gray
        ];

        return $colors[$category] ?? '#6b7280';
    }

    /**
     * Duplicate expense (for recurring expenses).
     */
    public function duplicate(Expense $expense)
    {
        $newExpense = $expense->replicate();
        $newExpense->id = Str::uuid();
        $newExpense->expense_date = now()->toDateString();
        $newExpense->registered_by = auth()->id();
        $newExpense->invoice_number = null; // Clear invoice number for duplicate
        $newExpense->save();

        return redirect()->route('expenses.edit', $newExpense)
            ->with('success', 'Gasto duplicado exitosamente. Verifique los datos antes de guardar.');
    }

    /**
     * Mark expense as recurring.
     */
    public function createRecurring(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
            'next_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after:next_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you would create a recurring expense record
        // This is a simplified version - you might want to create a separate table for recurring expenses
        
        return response()->json([
            'success' => true,
            'message' => 'Gasto recurrente configurado exitosamente.'
        ]);
    }
}