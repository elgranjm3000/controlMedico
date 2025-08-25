<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalServiceController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', PatientController::class);
    Route::patch('patients/{patient}/activate', [PatientController::class, 'activate'])->name('patients.activate');
    Route::get('api/patients/search', [PatientController::class, 'search'])->name('api.patients.search');
    Route::get('api/patients/{patient}/data', [PatientController::class, 'getPatientData'])->name('api.patients.data');

    // Doctors Management
    Route::resource('doctors', DoctorController::class);
    Route::patch('doctors/{doctor}/activate', [DoctorController::class, 'activate'])->name('doctors.activate');
    Route::get('api/doctors/search', [DoctorController::class, 'search'])->name('api.doctors.search');
    Route::get('api/doctors/{doctor}/data', [DoctorController::class, 'getDoctorData'])->name('api.doctors.data');
    Route::get('api/doctors/{doctor}/schedule', [DoctorController::class, 'getSchedule'])->name('api.doctors.schedule');
    
    // Appointments Management
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::get('api/appointments/calendar', [AppointmentController::class, 'getCalendarData'])->name('api.appointments.calendar');
    Route::get('api/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('api.appointments.available-slots');
    
 
    // Medical Services Management
    Route::resource('medical-services', MedicalServiceController::class);
    Route::patch('medical-services/{medicalService}/activate', [MedicalServiceController::class, 'activate'])->name('medical-services.activate');
    Route::get('api/medical-services/search', [MedicalServiceController::class, 'search'])->name('api.medical-services.search');
    
    // Inventory Items Management
      Route::resource('inventory', InventoryController::class)->names([
        'index' => 'inventory.index',
        'create' => 'inventory.create',
        'store' => 'inventory.store',
        'show' => 'inventory.show',
        'edit' => 'inventory.edit',
        'update' => 'inventory.update',
        'destroy' => 'inventory.destroy'
    ])->parameters(['inventory' => 'inventoryItem']);

     Route::get('/inventory/lowStock', [InventoryController::class, 'lowStock'])
             ->name('inventory.lowStock');
        Route::prefix('inventory/{inventoryItem}')->name('inventory.')->group(function () {
        // Agregar stock
        Route::get('/add-stock', [InventoryController::class, 'showAddStock'])
             ->name('showAddStock');
       
        Route::post('/add-stock', [InventoryController::class, 'addStock'])
             ->name('addStock');
        
        // Remover stock
        Route::get('/remove-stock', [InventoryController::class, 'showRemoveStock'])
             ->name('showRemoveStock');
        Route::post('/remove-stock', [InventoryController::class, 'removeStock'])
             ->name('removeStock');
        
        // Ver movimientos específicos del producto
        Route::get('/movements', [InventoryController::class, 'movements'])
             ->name('movements');
        
        // Toggle status del producto
        Route::patch('/toggle-status', [InventoryController::class, 'toggleStatus'])
             ->name('toggleStatus');
    });
    
    // Inventory Movements Management
    
    // Invoices Management
    Route::resource('invoices', InvoiceController::class);
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendByEmail'])->name('invoices.send-email');
    
    // Expenses Management
    Route::resource('expenses', ExpenseController::class);
    Route::get('api/expenses/categories-summary', [ExpenseController::class, 'getCategoriesSummary'])->name('api.expenses.categories-summary');
    
    // Reports (can be added later)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('financial', function () { return view('reports.financial'); })->name('financial');
        Route::get('appointments', function () { return view('reports.appointments'); })->name('appointments');
        Route::get('inventory', function () { return view('reports.inventory'); })->name('inventory');
        Route::get('patients', function () { return view('reports.patients'); })->name('patients');
    });
    
    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        
        // Dashboard data
        Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('dashboard/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('dashboard.recent-activities');
        
        // Quick stats
        Route::get('stats/monthly-income', function () {
            return response()->json(['income' => \App\Models\Invoice::whereMonth('created_at', now()->month)->where('status', 'pagada')->sum('total')]);
        })->name('stats.monthly-income');
        
        Route::get('stats/monthly-expenses', function () {
            return response()->json(['expenses' => \App\Models\Expense::whereMonth('expense_date', now()->month)->sum('amount')]);
        })->name('stats.monthly-expenses');
        
        Route::get('stats/today-appointments', function () {
            return response()->json(['count' => \App\Models\Appointment::whereDate('scheduled_at', today())->count()]);
        })->name('stats.today-appointments');
        
        // System health check
        Route::get('health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now(),
                'database' => 'connected',
                'memory_usage' => memory_get_usage(true),
            ]);
        })->name('health');
    });
    
});

