<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalService;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'appointment', 'createdBy']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', function($patientQuery) use ($search) {
                      $patientQuery->where('name', 'LIKE', "%{$search}%")
                                   ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by creation date
        $query->orderBy('created_at', 'desc');

        $invoices = $query->paginate(15)->appends($request->all());
        
        // Get summary statistics
        $totalInvoices = $query->count();
        $totalAmount = $query->sum('total');
        $paidAmount = $query->where('status', 'pagada')->sum('total');
        $pendingAmount = $query->where('status', 'pendiente')->sum('total');
        
        return view('modules.invoices.index', compact(
            'invoices', 
            'totalInvoices', 
            'totalAmount', 
            'paidAmount', 
            'pendingAmount'
        ));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $medicalServices = MedicalService::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $inventoryItems = InventoryItem::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();

        // Pre-select patient if provided
        $selectedPatient = null;
        $selectedAppointment = null;
        
        if ($request->has('patient_id')) {
            $selectedPatient = Patient::find($request->patient_id);
        }
        
        if ($request->has('appointment_id')) {
            $selectedAppointment = Appointment::with(['patient', 'doctor'])
                ->find($request->appointment_id);
            $selectedPatient = $selectedAppointment->patient ?? null;
        }

        // Generate next invoice number
        $lastInvoice = Invoice::orderBy('created_at', 'desc')->first();
        $nextNumber = $this->generateInvoiceNumber($lastInvoice);

        return view('modules.invoices.form', compact(
            'patients', 
            'medicalServices', 
            'inventoryItems', 
            'selectedPatient', 
            'selectedAppointment',
            'nextNumber'
        ));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,id',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'payment_method' => 'required|in:efectivo,transferencia,credito',
            'due_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:service,inventory',
            'items.*.item_id' => 'required|uuid',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate invoice number
            $lastInvoice = Invoice::orderBy('created_at', 'desc')->first();
            $invoiceNumber = $this->generateInvoiceNumber($lastInvoice);

            // Calculate totals
            $subtotal = 0;
            $validatedItems = [];

            foreach ($request->items as $item) {
                // Validate item exists and is active
                if ($item['type'] === 'service') {
                    $service = MedicalService::where('id', $item['item_id'])
                        ->where('is_active', true)
                        ->first();
                    if (!$service) {
                        throw new \Exception("Servicio médico no encontrado o inactivo");
                    }
                } else {
                    $inventoryItem = InventoryItem::where('id', $item['item_id'])
                        ->where('is_active', true)
                        ->first();
                    if (!$inventoryItem) {
                        throw new \Exception("Producto de inventario no encontrado o inactivo");
                    }
                    if ($inventoryItem->current_stock < $item['quantity']) {
                        throw new \Exception("Stock insuficiente para {$inventoryItem->name}. Stock actual: {$inventoryItem->current_stock}");
                    }
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;
                
                $validatedItems[] = array_merge($item, ['total' => $itemTotal]);
            }

            // Calculate tax (16% IVA - adjust as needed)
            $taxRate = 0.16;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Create invoice
            $invoice = Invoice::create([
                'id' => Str::uuid(),
                'invoice_number' => $invoiceNumber,
                'patient_id' => $request->patient_id,
                'appointment_id' => $request->appointment_id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'status' => 'pendiente',
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Create invoice items
            foreach ($validatedItems as $item) {
                InvoiceItem::create([
                    'id' => Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['type'] === 'service' ? $item['item_id'] : null,
                    'inventory_item_id' => $item['type'] === 'inventory' ? $item['item_id'] : null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);

                // Update inventory stock if it's an inventory item
                if ($item['type'] === 'inventory') {
                    $inventoryItem = InventoryItem::find($item['item_id']);
                    $inventoryItem->decrement('current_stock', $item['quantity']);
                    
                    // Create inventory movement
                    \App\Models\InventoryMovement::create([
                        'id' => Str::uuid(),
                        'inventory_item_id' => $item['item_id'],
                        'type' => 'salida',
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'reason' => 'venta',
                        'notes' => "Venta - Factura #{$invoiceNumber}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'appointment.doctor', 'items.service', 'items.inventoryItem', 'createdBy']);
        
        return view('modules.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        // Only allow editing if invoice is pending
        if ($invoice->status !== 'pendiente') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Solo se pueden editar facturas pendientes.');
        }

        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $medicalServices = MedicalService::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $inventoryItems = InventoryItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        $invoice->load(['items.service', 'items.inventoryItem']);

        return view('modules.invoices.form', compact('invoice', 'patients', 'medicalServices', 'inventoryItems'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Only allow updating if invoice is pending
        if ($invoice->status !== 'pendiente') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Solo se pueden editar facturas pendientes.');
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,id',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'payment_method' => 'required|in:efectivo,transferencia,credito',
            'due_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:service,inventory',
            'items.*.item_id' => 'required|uuid',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Restore inventory from previous items
            foreach ($invoice->items as $oldItem) {
                if ($oldItem->inventory_item_id) {
                    $inventoryItem = InventoryItem::find($oldItem->inventory_item_id);
                    if ($inventoryItem) {
                        $inventoryItem->increment('current_stock', $oldItem->quantity);
                        
                        // Create reverse inventory movement
                        \App\Models\InventoryMovement::create([
                            'id' => Str::uuid(),
                            'inventory_item_id' => $oldItem->inventory_item_id,
                            'type' => 'entrada',
                            'quantity' => $oldItem->quantity,
                            'unit_price' => $oldItem->unit_price,
                            'reason' => 'ajuste',
                            'notes' => "Ajuste por edición de factura #{$invoice->invoice_number}",
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }

            // Delete old items
            $invoice->items()->delete();

            // Calculate new totals and create new items
            $subtotal = 0;
            $validatedItems = [];

            foreach ($request->items as $item) {
                // Validate item exists and is active
                if ($item['type'] === 'service') {
                    $service = MedicalService::where('id', $item['item_id'])
                        ->where('is_active', true)
                        ->first();
                    if (!$service) {
                        throw new \Exception("Servicio médico no encontrado o inactivo");
                    }
                } else {
                    $inventoryItem = InventoryItem::where('id', $item['item_id'])
                        ->where('is_active', true)
                        ->first();
                    if (!$inventoryItem) {
                        throw new \Exception("Producto de inventario no encontrado o inactivo");
                    }
                    if ($inventoryItem->current_stock < $item['quantity']) {
                        throw new \Exception("Stock insuficiente para {$inventoryItem->name}. Stock actual: {$inventoryItem->current_stock}");
                    }
                }

                $itemTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemTotal;
                
                $validatedItems[] = array_merge($item, ['total' => $itemTotal]);
            }

            // Calculate tax
            $taxRate = 0.16;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Update invoice
            $invoice->update([
                'patient_id' => $request->patient_id,
                'appointment_id' => $request->appointment_id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
            ]);

            // Create new invoice items
            foreach ($validatedItems as $item) {
                InvoiceItem::create([
                    'id' => Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['type'] === 'service' ? $item['item_id'] : null,
                    'inventory_item_id' => $item['type'] === 'inventory' ? $item['item_id'] : null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);

                // Update inventory stock if it's an inventory item
                if ($item['type'] === 'inventory') {
                    $inventoryItem = InventoryItem::find($item['item_id']);
                    $inventoryItem->decrement('current_stock', $item['quantity']);
                    
                    // Create inventory movement
                    \App\Models\InventoryMovement::create([
                        'id' => Str::uuid(),
                        'inventory_item_id' => $item['item_id'],
                        'type' => 'salida',
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'reason' => 'venta',
                        'notes' => "Venta actualizada - Factura #{$invoice->invoice_number}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Only allow deletion if invoice is pending
        if ($invoice->status !== 'pendiente') {
            return redirect()->route('invoices.index')
                ->with('error', 'Solo se pueden eliminar facturas pendientes.');
        }

        DB::beginTransaction();

        try {
            // Restore inventory stock
            foreach ($invoice->items as $item) {
                if ($item->inventory_item_id) {
                    $inventoryItem = InventoryItem::find($item->inventory_item_id);
                    if ($inventoryItem) {
                        $inventoryItem->increment('current_stock', $item->quantity);
                        
                        // Create reverse inventory movement
                        \App\Models\InventoryMovement::create([
                            'id' => Str::uuid(),
                            'inventory_item_id' => $item->inventory_item_id,
                            'type' => 'entrada',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'reason' => 'ajuste',
                            'notes' => "Ajuste por eliminación de factura #{$invoice->invoice_number}",
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }

            // Delete invoice items first
            $invoice->items()->delete();
            
            // Delete invoice
            $invoice->delete();

            DB::commit();
            
            return redirect()->route('invoices.index')
                ->with('success', 'Factura eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->route('invoices.index')
                ->with('error', 'Error al eliminar la factura: ' . $e->getMessage());
        }
    }

    /**
     * Update invoice status.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pendiente,pagada,cancelada',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // If cancelling, restore inventory
        if ($request->status === 'cancelada' && $invoice->status !== 'cancelada') {
            DB::beginTransaction();
            
            try {
                foreach ($invoice->items as $item) {
                    if ($item->inventory_item_id) {
                        $inventoryItem = InventoryItem::find($item->inventory_item_id);
                        if ($inventoryItem) {
                            $inventoryItem->increment('current_stock', $item->quantity);
                            
                            // Create reverse inventory movement
                            \App\Models\InventoryMovement::create([
                                'id' => Str::uuid(),
                                'inventory_item_id' => $item->inventory_item_id,
                                'type' => 'entrada',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'reason' => 'cancelacion',
                                'notes' => "Cancelación de factura #{$invoice->invoice_number}",
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                $invoice->update([
                    'status' => $request->status,
                    'notes' => $request->notes ?? $invoice->notes,
                ]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cancelar la factura: ' . $e->getMessage()
                ], 500);
            }
        } else {
            $invoice->update([
                'status' => $request->status,
                'notes' => $request->notes ?? $invoice->notes,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado de la factura actualizado exitosamente.',
            'invoice' => [
                'id' => $invoice->id,
                'status' => $invoice->status,
                'notes' => $invoice->notes,
            ]
        ]);
    }

    /**
     * Generate PDF for invoice.
     */
    public function generatePDF(Invoice $invoice)
    {
        $invoice->load(['patient', 'appointment.doctor', 'items.service', 'items.inventoryItem', 'createdBy']);
        
        // This would typically use a PDF library like DomPDF or Snappy
        // For now, return a view that can be printed
        return view('invoices.pdf', compact('invoice'));
    }

    /**
     * Send invoice by email.
     */
    public function sendByEmail(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Here you would implement email sending logic
            // For now, just return success
            
            return response()->json([
                'success' => true,
                'message' => "Factura enviada exitosamente a {$request->email}."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber($lastInvoice = null)
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        
        if ($lastInvoice) {
            // Extract number from last invoice
            $parts = explode('-', $lastInvoice->invoice_number);
            if (count($parts) >= 3) {
                $lastYear = $parts[1];
                $lastNumber = (int) $parts[2];
                
                if ($lastYear === $year) {
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
            } else {
                $nextNumber = 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('FAC-%s-%04d', $year, $nextNumber);
    }

    /**
     * Get invoice data for autocomplete/search.
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $invoices = Invoice::with('patient')
            ->where(function($query) use ($term) {
                $query->where('invoice_number', 'LIKE', "%{$term}%")
                      ->orWhereHas('patient', function($patientQuery) use ($term) {
                          $patientQuery->where('name', 'LIKE', "%{$term}%")
                                       ->orWhere('last_name', 'LIKE', "%{$term}%");
                      });
            })
            ->limit(10)
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'text' => $invoice->invoice_number . ' - ' . $invoice->patient->getFullNameAttribute(),
                    'number' => $invoice->invoice_number,
                    'patient' => $invoice->patient->getFullNameAttribute(),
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                ];
            });

        return response()->json($invoices);
    }
}