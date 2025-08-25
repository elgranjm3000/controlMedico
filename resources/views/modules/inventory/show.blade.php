@extends('layouts.app')
@section('title', 'Producto: ' . $inventoryItem->name)
@section('content')
<div class="main-content">
    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventario</a></li>
                        <li class="breadcrumb-item active">{{ $inventoryItem->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <!-- Product Information -->
            <div class="col-md-8">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-box widget-icon"></i>
                            <h5>{{ $inventoryItem->name }}</h5>
                            <span class="widget-subtitle">Código: {{ $inventoryItem->code }}</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            <a href="{{ route('inventory.edit', $inventoryItem) }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i>Acciones
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.showAddStock', $inventoryItem) }}">
                                            <i class="fas fa-plus me-2"></i>Agregar Stock
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.showRemoveStock', $inventoryItem) }}">
                                            <i class="fas fa-minus me-2"></i>Remover Stock
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('inventory.movements', $inventoryItem) }}">
                                            <i class="fas fa-history me-2"></i>Ver Todos los Movimientos
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item" onclick="toggleStatus('{{ $inventoryItem->id }}')">
                                            <i class="fas fa-toggle-{{ $inventoryItem->is_active ? 'off' : 'on' }} me-2"></i>
                                            {{ $inventoryItem->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Product Details -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <h6>Información General</h6>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <strong>Nombre:</strong> {{ $inventoryItem->name }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Código:</strong> {{ $inventoryItem->code }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Categoría:</strong> 
                                            <span class="badge bg-light text-dark">{{ ucfirst($inventoryItem->category) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Unidad:</strong> {{ $inventoryItem->unit_measure }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Estado:</strong> 
                                            <span class="status-badge {{ $inventoryItem->is_active ? 'status-pagada' : 'status-cancelada' }}">
                                                {{ $inventoryItem->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                        @if($inventoryItem->description)
                                        <div class="info-item">
                                            <strong>Descripción:</strong><br>
                                            <span class="text-muted">{{ $inventoryItem->description }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <h6>Stock e Inventario</h6>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <strong>Stock Actual:</strong>
                                            <span class="stock-badge-lg {{ $isLowStock ? 'stock-low' : ($inventoryItem->current_stock == 0 ? 'stock-zero' : 'stock-normal') }}">
                                                {{ $inventoryItem->current_stock }} {{ $inventoryItem->unit_measure }}
                                            </span>
                                            @if($isLowStock)
                                                <small class="text-warning d-block">
                                                    <i class="fas fa-exclamation-triangle"></i> Stock bajo
                                                </small>
                                            @endif
                                        </div>
                                        <div class="info-item">
                                            <strong>Stock Mínimo:</strong> {{ $inventoryItem->minimum_stock }} {{ $inventoryItem->unit_measure }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Precio Unitario:</strong> 
                                            <span class="text-success fw-bold">${{ number_format($inventoryItem->unit_price, 2) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Valor Total Stock:</strong> 
                                            <span class="text-success fw-bold">${{ number_format($inventoryItem->current_stock * $inventoryItem->unit_price, 2) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Total Movimientos:</strong> {{ $totalMovements }}
                                        </div>
                                        @if($lastMovement)
                                        <div class="info-item">
                                            <strong>Último Movimiento:</strong><br>
                                            <small class="text-muted">
                                                {{ ucfirst($lastMovement->type === 'entrada' ? 'Entrada' : 'Salida') }} - 
                                                {{ $lastMovement->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Actions -->
            <div class="col-md-4">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-tools widget-icon"></i>
                            <h5>Acciones Rápidas</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('inventory.showAddStock', $inventoryItem) }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Agregar Stock
                            </a>
                            <a href="{{ route('inventory.showRemoveStock', $inventoryItem) }}" class="btn btn-warning">
                                <i class="fas fa-minus me-2"></i>Remover Stock
                            </a>
                            <a href="{{ route('inventory.edit', $inventoryItem) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar Producto
                            </a>
                            <a href="{{ route('inventory.movements', $inventoryItem) }}" class="btn btn-outline-info">
                                <i class="fas fa-history me-2"></i>Ver Movimientos
                            </a>
                        </div>
                        
                        @if($isLowStock)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡Alerta de Stock Bajo!</strong><br>
                            El stock actual está por debajo del mínimo requerido.
                        </div>
                        @endif
                        
                        @if($inventoryItem->current_stock == 0)
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>¡Sin Stock!</strong><br>
                            Este producto no tiene existencias.
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-chart-bar widget-icon"></i>
                            <h5>Estadísticas</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="stat-item">
                            <div class="stat-label">Entradas Totales</div>
                            <div class="stat-value text-success">
                                {{ $movements->where('type', 'entrada')->sum('quantity') }}
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Salidas Totales</div>
                            <div class="stat-value text-danger">
                                {{ $movements->where('type', 'salida')->sum('quantity') }}
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Rotación (mes actual)</div>
                            <div class="stat-value text-info">
                                {{ $movements->where('type', 'OUT')->where('created_at', '>=', now()->startOfMonth())->sum('quantity') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Movements -->
        @if($movements->count() > 0)
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-history widget-icon"></i>
                            <h5>Movimientos Recientes</h5>
                            <span class="widget-subtitle">Últimos {{ $movements->count() }} movimientos</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.movements', $inventoryItem) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list me-1"></i>Ver Todos
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Motivo</th>
                                        <th>Usuario</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                    <tr>
                                        <td>
                                            <div class="date-info">
                                                <strong>{{ $movement->created_at->format('d/m/Y') }}</strong>
                                                <small class="text-muted d-block">{{ $movement->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="movement-badge movement-{{ strtolower($movement->type) }}">
                                                <i class="fas fa-{{ $movement->type === 'IN' ? 'arrow-down' : 'arrow-up' }}"></i>
                                                {{ $movement->type === 'entrada' ? 'Entrada' : 'Salida' }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="{{ $movement->type === 'entrada' ? 'text-success' : 'text-danger' }}">
                                                {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }}
                                            </strong>
                                            <small class="text-muted d-block">{{ $inventoryItem->unit_measure }}</small>
                                        </td>
                                        <td>
                                            @switch($movement->reason)
                                                @case('PURCHASE')
                                                    <span class="badge bg-success">Compra</span>
                                                    @break
                                                @case('SALE')
                                                    <span class="badge bg-primary">Venta</span>
                                                    @break
                                                @case('ADJUSTMENT')
                                                    <span class="badge bg-warning">Ajuste</span>
                                                    @break
                                                @case('INITIAL_STOCK')
                                                    <span class="badge bg-info">Stock Inicial</span>
                                                    @break
                                                @case('DAMAGE')
                                                    <span class="badge bg-danger">Daño</span>
                                                    @break
                                                @case('EXPIRED')
                                                    <span class="badge bg-secondary">Vencido</span>
                                                    @break
                                                @case('RETURN')
                                                    <span class="badge bg-light text-dark">Devolución</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ ucfirst($movement->reason) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($movement->user)
                                                <strong>{{ $movement->user->name }}</strong>
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($movement->notes)
                                                <span class="text-muted">{{ Str::limit($movement->notes, 30) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>
@endsection

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
</script>
@endpush

@push('styles')
<style>
.info-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    height: 100%;
}

.info-card-header {
    padding: 1rem 1.25rem 0.5rem;
    border-bottom: 1px solid #e5e7eb;
    background-color: #f9fafb;
    border-radius: 0.5rem 0.5rem 0 0;
}

.info-card-header h6 {
    margin: 0;
    color: #374151;
    font-weight: 600;
}

.info-card-body {
    padding: 1.25rem;
}

.info-item {
    margin-bottom: 0.75rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.stock-badge-lg {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
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

.movement-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.movement-in {
    background-color: #d1fae5;
    color: #065f46;
}

.movement-out {
    background-color: #fee2e2;
    color: #991b1b;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
}

.stat-value {
    font-weight: 600;
    font-size: 1.1rem;
}

.date-info strong {
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .widget-actions {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .widget-actions .btn {
        flex: 1;
        min-width: 120px;
    }
}
</style>
@endpush
