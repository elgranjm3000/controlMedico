@extends('layouts.app')
@section('title', 'Movimientos - ' . $inventoryItem->name)
@section('content')
<div class="main-content">
    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventario</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.show', $inventoryItem) }}">{{ $inventoryItem->name }}</a></li>
                        <li class="breadcrumb-item active">Movimientos</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-history widget-icon"></i>
                            <h5>Historial de Movimientos</h5>
                            <span class="widget-subtitle">{{ $inventoryItem->name }} - {{ $movements->total() }} movimientos registrados</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Producto
                            </a>
                            <a href="{{ route('inventory.showAddStock', $inventoryItem) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>Agregar Stock
                            </a>
                            <a href="{{ route('inventory.showRemoveStock', $inventoryItem) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-minus me-1"></i>Remover Stock
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Product Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-primary">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-label">Stock Actual</div>
                                        <div class="stat-value text-primary">{{ $inventoryItem->current_stock }} {{ $inventoryItem->unit_measure }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-success">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-label">Total Entradas</div>
                                        <div class="stat-value text-success">{{ $movements->where('type', 'entrada')->sum('quantity') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-danger">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-label">Total Salidas</div>
                                        <div class="stat-value text-danger">{{ $movements->where('type', 'salida')->sum('quantity') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-info">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-label">Valor Actual</div>
                                        <div class="stat-value text-info">${{ number_format($inventoryItem->current_stock * $inventoryItem->unit_price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($movements->count() > 0)
                            <!-- Filters -->
                            <form method="GET" class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">Todos los tipos</option>
                                        <option value="IN" {{ request('type') == 'entrada' ? 'selected' : '' }}>Entradas</option>
                                        <option value="OUT" {{ request('type') == 'salida' ? 'selected' : '' }}>Salidas</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="reason" class="form-select">
                                        <option value="">Todos los motivos</option>
                                        <option value="PURCHASE" {{ request('reason') == 'PURCHASE' ? 'selected' : '' }}>Compra</option>
                                        <option value="SALE" {{ request('reason') == 'SALE' ? 'selected' : '' }}>Venta</option>
                                        <option value="ADJUSTMENT" {{ request('reason') == 'ADJUSTMENT' ? 'selected' : '' }}>Ajuste</option>
                                        <option value="INITIAL_STOCK" {{ request('reason') == 'INITIAL_STOCK' ? 'selected' : '' }}>Stock Inicial</option>
                                        <option value="DAMAGE" {{ request('reason') == 'DAMAGE' ? 'selected' : '' }}>Daño</option>
                                        <option value="EXPIRED" {{ request('reason') == 'EXPIRED' ? 'selected' : '' }}>Vencido</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Desde">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Hasta">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrar
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio Unit.</th>
                                            <th class="text-end">Valor Total</th>
                                            <th>Motivo</th>
                                            <th>Usuario</th>
                                            <th>Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($movements as $movement)
                                        <tr class="{{ $movement->type === 'salida' ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="date-info">
                                                    <strong>{{ $movement->created_at->format('d/m/Y') }}</strong>
                                                    <small class="text-muted d-block">{{ $movement->created_at->format('H:i:s') }}</small>
                                                    <small class="text-muted">{{ $movement->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="movement-badge movement-{{ strtolower($movement->type) }}">
                                                    <i class="fas fa-{{ $movement->type === 'entrada' ? 'arrow-down' : 'arrow-up' }}"></i>
                                                    {{ $movement->type === 'entrada' ? 'Entrada' : 'Salida' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong class="{{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                                    {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }}
                                                </strong>
                                                <small class="text-muted d-block">{{ $inventoryItem->unit_measure }}</small>
                                            </td>
                                            <td class="text-end">
                                                <strong>${{ number_format($movement->unit_price, 2) }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <strong class="{{ $movement->type === 'entrada' ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($movement->quantity * $movement->unit_price, 2) }}
                                                </strong>
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
                                                    @case('PRODUCTION')
                                                        <span class="badge bg-purple">Producción</span>
                                                        @break
                                                    @case('THEFT')
                                                        <span class="badge bg-dark">Robo/Pérdida</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ ucfirst($movement->reason) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($movement->user)
                                                    <div class="user-info">
                                                        <strong>{{ $movement->user->name }}</strong>
                                                        <small class="text-muted d-block">{{ ucfirst($movement->user->role ?? 'Usuario') }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-robot"></i> Sistema
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($movement->notes)
                                                    <span class="text-muted" title="{{ $movement->notes }}" data-bs-toggle="tooltip">
                                                        {{ Str::limit($movement->notes, 30) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="results-info">
                                    <small class="text-muted">
                                        Mostrando {{ $movements->firstItem() }} a {{ $movements->lastItem() }} 
                                        de {{ $movements->total() }} movimientos
                                    </small>
                                </div>
                                {{ $movements->withQueryString()->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-history"></i></div>
                                <h6>No hay movimientos registrados</h6>
                                <p>Este producto aún no tiene movimientos de inventario registrados.</p>
                                <div class="empty-actions">
                                    <a href="{{ route('inventory.showAddStock', $inventoryItem) }}" class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i>Agregar Primer Stock
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('styles')
<style>
.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.5rem;
    height: 100%;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    font-size: 2rem;
    margin-right: 1rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 0.5rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
}

.movement-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.movement-in {
    background-color: #d1fae5;
    color: #065f46;
}

.movement-out {
    background-color: #fee2e2;
    color: #991b1b;
}

.date-info strong {
    display: block;
    line-height: 1.2;
}

.date-info small {
    line-height: 1.1;
}

.user-info strong {
    display: block;
    line-height: 1.2;
}

.bg-purple {
    background-color: #8b5cf6 !important;
}

.table-warning {
    --bs-table-bg: rgba(255, 193, 7, 0.1);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #94a3b8;
    margin-bottom: 1.5rem;
}

.empty-state h6 {
    color: #475569;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 2rem;
    font-size: 1rem;
}

.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.results-info {
    flex: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .widget-actions {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .widget-actions .btn {
        flex: 1;
        min-width: 120px;
    }
    
    .empty-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .empty-actions .btn {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
}
</style>
@endpush
