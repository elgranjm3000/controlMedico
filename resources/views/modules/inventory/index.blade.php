@extends('layouts.app')
@section('title', 'Control de Inventario')
@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Control de Inventario</h2>
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
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventario</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="kpi-card income-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
                        <div class="kpi-metric"><span>{{ $totalItems }}</span><small>Total</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Productos</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card appointments-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="kpi-metric"><span>{{ $activeItems }}</span><small>Activos</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">En Uso</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card expense-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="kpi-metric"><span>{{ $lowStockItems }}</span><small>Alertas</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Stock Bajo</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card profit-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="kpi-metric"><span>${{ number_format($items->sum(function($item) { return $item->current_stock * $item->unit_price; }), 2) }}</span><small>Valor</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Total Inventario</div></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-boxes widget-icon"></i>
                            <h5>Lista de Productos</h5>
                            <span class="widget-subtitle">{{ $items->total() }} productos registrados</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Nuevo Producto
                            </a>
                            <a href="{{ route('inventory.lowStock') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-exclamation-triangle me-1"></i>Stock Bajo
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Filters -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar producto..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ ucfirst($category) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-check-label">
                                    <input type="checkbox" name="low_stock" value="1" 
                                           {{ request('low_stock') ? 'checked' : '' }} class="form-check-input me-2">
                                    Solo stock bajo
                                </label>
                            </div>
                            <div class="col-md-2">
                                <select name="sort_by" class="form-select">
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nombre</option>
                                    <option value="current_stock" {{ request('sort_by') == 'current_stock' ? 'selected' : '' }}>Stock</option>
                                    <option value="unit_price" {{ request('sort_by') == 'unit_price' ? 'selected' : '' }}>Precio</option>
                                    <option value="category" {{ request('sort_by') == 'category' ? 'selected' : '' }}>Categoría</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </form>

                        @if($items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th class="text-center">Stock</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-center">Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $item->code }}</strong>
                                                @if($item->isLowStock())
                                                    <small class="text-warning d-block">
                                                        <i class="fas fa-exclamation-triangle"></i> Stock bajo
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('inventory.show', $item) }}" class="contact-link">
                                                    <strong>{{ $item->name }}</strong>
                                                </a>
                                                @if($item->description)
                                                    <small class="text-muted d-block">{{ Str::limit($item->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ ucfirst($item->category) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="stock-badge {{ $item->isLowStock() ? 'stock-low' : ($item->current_stock == 0 ? 'stock-zero' : 'stock-normal') }}">
                                                    {{ $item->current_stock }} {{ $item->unit_measure }}
                                                </span>
                                                <small class="text-muted d-block">
                                                    Min: {{ $item->minimum_stock }}
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">${{ number_format($item->unit_price, 2) }}</strong>
                                                <small class="text-muted d-block">
                                                    Total: ${{ number_format($item->current_stock * $item->unit_price, 2) }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge {{ $item->is_active ? 'status-pagada' : 'status-cancelada' }}">
                                                    {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('inventory.show', $item) }}" 
                                                       class="btn btn-outline-primary btn-sm" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('inventory.edit', $item) }}" 
                                                       class="btn btn-outline-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" 
                                                                   href="{{ route('inventory.showAddStock', $item) }}">
                                                                    <i class="fas fa-plus me-2"></i>Agregar Stock
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" 
                                                                   href="{{ route('inventory.showRemoveStock', $item) }}">
                                                                    <i class="fas fa-minus me-2"></i>Remover Stock
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <button class="dropdown-item" 
                                                                        onclick="toggleStatus('{{ $item->id }}')">
                                                                    <i class="fas fa-toggle-{{ $item->is_active ? 'off' : 'on' }} me-2"></i>
                                                                    {{ $item->is_active ? 'Desactivar' : 'Activar' }}
                                                                </button>
                                                            </li>
                                                            @if($item->movements->count() == 0 && $item->invoiceItems->count() == 0)
                                                            <li>
                                                                <button class="dropdown-item text-danger" 
                                                                        onclick="deleteItem('{{ $item->id }}')">
                                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                                </button>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $items->links() }}
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-boxes"></i></div>
                                <h6>No hay productos registrados</h6>
                                <p>Comience agregando el primer producto al inventario.</p>
                                <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Crear Primer Producto
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
function toggleStatus(itemId) {
    if (confirm('¿Cambiar el estado de este producto?')) {
        axios.patch(`/inventory/${itemId}/toggle-status`)
            .then(response => {
                showGlobalAlert('Estado actualizado exitosamente', 'success');
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                showGlobalAlert('Error al cambiar el estado', 'error');
            });
    }
}

function deleteItem(itemId) {
    if (confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.')) {
        axios.delete(`/inventory/${itemId}`)
            .then(response => {
                showGlobalAlert('Producto eliminado exitosamente', 'success');
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                const message = error.response?.data?.message || 'Error al eliminar el producto';
                showGlobalAlert(message, 'error');
            });
    }
}
</script>
@endpush

@push('styles')
<style>
.stock-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
}

.stock-normal {
    background-color: #d1fae5;
    color: #065f46;
}

.stock-low {
    background-color: #fef3c7;
    color: #92400e;
}

.stock-zero {
    background-color: #fee2e2;
    color: #991b1b;
}

.table-warning {
    --bs-table-bg: #fff3cd;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.empty-state h6 {
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}
</style>
@endpush
