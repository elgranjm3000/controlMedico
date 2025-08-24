@extends('layouts.app')

@section('title', 'Gestión de Gastos')

@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Gestión de Gastos</h2>
        </div>
        <div class="header-right">
            <div class="system-status">
                <div class="status-dot"></div>
                <span>Sistema Operativo</span>
            </div>
            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <!-- Breadcrumb -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Gastos</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="kpi-card expense-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="kpi-metric">
                            <span>${{ number_format($totalExpenses, 2) }}</span>
                            <small>Total Filtrado</small>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Gastos Totales</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card profit-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="kpi-metric">
                            <span>${{ number_format($monthlyExpenses, 2) }}</span>
                            <small>Este Mes</small>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Gastos Mensuales</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card appointments-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="kpi-metric">
                            <span>{{ $expenses->total() }}</span>
                            <small>Registros</small>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Total Gastos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card income-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="kpi-metric">
                            <span>{{ $expensesByCategory->count() }}</span>
                            <small>Categorías</small>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Categorías Activas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-receipt widget-icon"></i>
                            <h5>Lista de Gastos</h5>
                            <span class="widget-subtitle">{{ $expenses->total() }} gastos registrados</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nuevo Gasto
                            </a>
                            <button class="btn btn-outline-success btn-sm" onclick="exportExpenses()">
                                <i class="fas fa-file-excel me-1"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Filters Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('expenses.index') }}" class="patient-filters">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Buscar</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        name="search" 
                                                        value="{{ request('search') }}"
                                                        placeholder="Proveedor, descripción...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Categoría</label>
                                                <select name="category" class="form-select">
                                                    <option value="">Todas</option>
                                                    <option value="servicios" {{ request('category') == 'servicios' ? 'selected' : '' }}>Servicios</option>
                                                    <option value="nomina" {{ request('category') == 'nomina' ? 'selected' : '' }}>Nómina</option>
                                                    <option value="honorarios_medicos" {{ request('category') == 'honorarios_medicos' ? 'selected' : '' }}>Honorarios Médicos</option>
                                                    <option value="insumos" {{ request('category') == 'insumos' ? 'selected' : '' }}>Insumos</option>
                                                    <option value="equipo" {{ request('category') == 'equipo' ? 'selected' : '' }}>Equipo</option>
                                                    <option value="otros" {{ request('category') == 'otros' ? 'selected' : '' }}>Otros</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Desde</label>
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="date_from" 
                                                    value="{{ request('date_from') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Hasta</label>
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="date_to" 
                                                    value="{{ request('date_to') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Pago</label>
                                                <select name="payment_method" class="form-select">
                                                    <option value="">Todos</option>
                                                    <option value="efectivo" {{ request('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                                    <option value="transferencia" {{ request('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                                    <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-1">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Expenses Table -->
                        @if($expenses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Categoría</th>
                                            <th>Descripción</th>
                                            <th>Monto</th>
                                            <th>Método Pago</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $expense)
                                            <tr>
                                                <td>
                                                    <div class="date-info">
                                                        <strong>{{ $expense->expense_date->format('d/m/Y') }}</strong>
                                                        <small class="text-muted d-block">{{ $expense->expense_date->diffForHumans() }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $expense->supplier_name }}</strong>
                                                        @if($expense->invoice_number)
                                                            <small class="text-muted d-block">Factura: {{ $expense->invoice_number }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($expense->category)
                                                        @case('servicios')
                                                            <span class="badge bg-primary">Servicios</span>
                                                            @break
                                                        @case('nomina')
                                                            <span class="badge bg-success">Nómina</span>
                                                            @break
                                                        @case('honorarios_medicos')
                                                            <span class="badge bg-info">Honorarios Médicos</span>
                                                            @break
                                                        @case('insumos')
                                                            <span class="badge bg-warning">Insumos</span>
                                                            @break
                                                        @case('equipo')
                                                            <span class="badge bg-danger">Equipo</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">Otros</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span title="{{ $expense->description }}">
                                                        {{ Str::limit($expense->description, 50) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">${{ number_format($expense->amount, 2) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fas fa-{{ $expense->payment_method === 'efectivo' ? 'money-bill' : ($expense->payment_method === 'transferencia' ? 'university' : 'check') }} me-1"></i>
                                                        {{ ucfirst($expense->payment_method) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-outline-info btn-sm" onclick="viewExpense('{{ $expense->id }}')" title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm" onclick="duplicateExpense('{{ $expense->id }}')" title="Duplicar">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Está seguro de eliminar este gasto?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($expenses->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="pagination-info">
                                        <small class="text-muted">
                                            Mostrando {{ $expenses->firstItem() }} a {{ $expenses->lastItem() }} 
                                            de {{ $expenses->total() }} resultados
                                        </small>
                                    </div>
                                    <div class="pagination-controls">
                                        {{ $expenses->links() }}
                                    </div>
                                </div>
                            @endif

                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h6>No hay gastos registrados</h6>
                                <p>Comience registrando el primer gasto del sistema.</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Registrar Primer Gasto
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Expense Details Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="expenseModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterSelects = document.querySelectorAll('select[name="category"], select[name="payment_method"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Handle search input with debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
});

function viewExpense(expenseId) {
    // Show expense details in modal
    const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
    const modalBody = document.getElementById('expenseModalBody');
    
    modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
    modal.show();
    
    // Simulate loading expense details
    setTimeout(() => {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Proveedor:</strong><br>
                    <span class="text-muted">Información del proveedor</span>
                </div>
                <div class="col-md-6">
                    <strong>Monto:</strong><br>
                    <span class="text-success">$0.00</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <strong>Descripción:</strong><br>
                    <p class="text-muted">Detalles del gasto...</p>
                </div>
            </div>
        `;
    }, 1000);
}

function duplicateExpense(expenseId) {
    if (confirm('¿Desea duplicar este gasto?')) {
        showGlobalAlert('Funcionalidad de duplicar próximamente disponible', 'info');
    }
}

function exportExpenses() {
    showGlobalAlert('Exportando gastos...', 'info');
    // Simulate export
    setTimeout(() => {
        showGlobalAlert('Exportación completada', 'success');
    }, 2000);
}
</script>
@endpush