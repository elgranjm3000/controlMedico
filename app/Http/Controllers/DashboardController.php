<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InventoryItem;

class DashboardController extends Controller
{
 
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Resumen financiero del mes
        $monthlyIncome = Invoice::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'pagada')
            ->sum('total');

        $monthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $profit = $monthlyIncome - $monthlyExpenses;

        // Estadísticas de citas
        $totalAppointments = Appointment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $completedAppointments = Appointment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'completada')
            ->count();

        $cancelledAppointments = Appointment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'cancelada')
            ->count();

        // Items con stock bajo
        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->where('is_active', true)
            ->get();

        // Citas de hoy
        $todayAppointments = Appointment::whereDate('scheduled_at', Carbon::today())
            ->with(['patient', 'doctor', 'consultationRoom'])
            ->orderBy('scheduled_at')
            ->get();

        return view('dashboard', compact(
            'monthlyIncome', 'monthlyExpenses', 'profit',
            'totalAppointments', 'completedAppointments', 'cancelledAppointments',
            'lowStockItems', 'todayAppointments'
        ));
    }
}