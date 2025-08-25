@extends('layouts.app')
@section('title', 'Resumen de Inventario')
@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Resumen de Inventario</h2>
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
                        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventario</a></li>
                        <li class="breadcrumb-item active">Resumen</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="summary-card total-products">
                    <div class="summary-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $totalItems }}</div>
                        <div class="summary-label">Total Productos</div>
                        <div class="summary-detail">{{ $activeItems }} activos</div>
                    </div>
                    <div class="summary-trend">
                        <i class="fas fa-chart-line text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="summary-card inventory-value">
                    <div class="summary-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">${{ number_format($totalValue, 0) }}</div>
                        <div class="summary-label">Valor Total</div>
                        <div class="summary-detail">Inventario completo</div>
                    </div>
                    <div class="summary-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="summary-card low-stock">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $lowStockItems }}</div>
                        <div class="summary-label">Stock Bajo</div>
                        <div class="summary-detail">Requieren atención</div>
                    </div>
                    <div class="summary-trend">
                        @if($lowStockItems > 0)
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        @else
                            <i class="fas fa-check-circle text-success"></i>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="summary-card out-stock">
                    <div class="summary-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $outOfStockItems }}</div>
                        <div class="summary-label">Sin Stock</div>
                        <div class="summary-detail">Crítico</div>
                    </div>
                    <div class="summary-trend">
                        @if($outOfStockItems > 0)
                            <i class="fas fa-times-circle text-danger"></i>
                        @else
                            <i class="fas fa-check-circle text-success"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Categories Distribution -->
            <div class="col-xl-8 col-lg-7">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-chart-pie widget-icon"></i>
                            <h5>Distribución por Categorías</h5>
                            <span class="widget-subtitle">Análisis de productos y valores por categoría</span>
                        </div>
                        <div class="widget-actions">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="viewMode" id="countView" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="countView">Cantidad</label>
                                <input type="radio" class="btn-check" name="viewMode" id="valueView" autocomplete="off">
                                <label class="btn btn-outline-primary" for="valueView">Valor</label>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body">
                        @if($categories->count() > 0)
                            <div class="categories-grid">
                                @foreach($categories as $category)
                                @php
                                    $percentage = $totalValue > 0 ? ($category->value / $totalValue) * 100 : 0;
                                    $countPercentage = $totalItems > 0 ? ($category->count / $totalItems) * 100 : 0;
                                @endphp
                                <div class="category-card">
                                    <div class="category-header">
                                        <div class="category-name">
                                            <h6>{{ ucfirst($category->category) }}</h6>
                                            <span class="category-products">{{ $category->count }} producto(s)</span>
                                        </div>
                                        <div class="category-value">
                                            <strong>${{ number_format($category->value, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="category-progress">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-animated" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%"
                                                 data-count-width="{{ $countPercentage }}%"
                                                 data-value-width="{{ $percentage }}%">
                                            </div>
                                        </div>
                                        <div class="progress-labels">
                                            <span class="progress-percentage">{{ number_format($percentage, 1) }}%</span>
                                            <span class="progress-count" style="display: none;">{{ number_format($countPercentage, 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-chart-pie"></i></div>
                                <h6>No hay categorías disponibles</h6>
                                <p>Agregue productos para ver la distribución por categorías.</p>
                                <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Crear Primer Producto
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Alerts -->
            <div class="col-xl-4 col-lg-5">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-bolt widget-icon"></i>
                            <h5>Acciones Rápidas</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="quick-actions-grid">
                            <a href="{{ route('inventory.create') }}" class="quick-action-card">
                                <div class="qa-icon bg-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="qa-content">
                                    <h6>Nuevo Producto</h6>
                                    <p>Agregar producto al inventario</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('inventory.lowStock') }}" class="quick-action-card">
                                <div class="qa-icon bg-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="qa-content">
                                    <h6>Stock Bajo</h6>
                                    <p>{{ $lowStockItems }} productos</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('inventory.index') }}" class="quick-action-card">
                                <div class="qa-icon bg-info">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="qa-content">
                                    <h6>Lista Completa</h6>
                                    <p>Ver todos los productos</p>
                                </div>
                            </a>
                            
                            <div class="quick-action-card" onclick="showReportOptions()">
                                <div class="qa-icon bg-secondary">
                                    <i class="fas fa-file-download"></i>
                                </div>
                                <div class="qa-content">
                                    <h6>Exportar</h6>
                                    <p>Generar reportes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts Section -->
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-bell widget-icon"></i>
                            <h5>Alertas del Sistema</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        @if($lowStockItems > 0 || $outOfStockItems > 0)
                            @if($outOfStockItems > 0)
                            <div class="alert alert-danger">
                                <div class="alert-icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="alert-content">
                                    <strong>Crítico:</strong> {{ $outOfStockItems }} producto(s) sin stock.
                                    <a href="{{ route('inventory.lowStock') }}" class="alert-link">Ver detalles</a>
                                </div>
                            </div>
                            @endif
                            
                            @if($lowStockItems > 0)
                            <div class="alert alert-warning">
                                <div class="alert-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="alert-content">
                                    <strong>Atención:</strong> {{ $lowStockItems }} producto(s) con stock bajo.
                                    <a href="{{ route('inventory.lowStock') }}" class="alert-link">Revisar ahora</a>
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="alert alert-success">
                                <div class="alert-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="alert-content">
                                    <strong>¡Excelente!</strong> Todos los productos tienen stock adecuado.
                                </div>
                            </div>
                        @endif

                        <div class="stats-mini">
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">Categorías:</span>
                                <span class="stat-mini-value">{{ $categories->count() }}</span>
                            </div>
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">Inactivos:</span>
                                <span class="stat-mini-value">{{ $totalItems - $activeItems }}</span>
                            </div>
                            <div class="stat-mini-item">
                                <span class="stat-mini-label">Valor Promedio:</span>
                                <span class="stat-mini-value">
                                    ${{ $activeItems > 0 ? number_format($totalValue / $activeItems, 2) : '0.00' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Movements -->
        @if($recentMovements && $recentMovements->count() > 0)
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-history widget-icon"></i>
                            <h5>Actividad Reciente</h5>
                            <span class="widget-subtitle">Últimos 10 movimientos del inventario</span>
                        </div>
                        <div class="widget-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshMovements()">
                                <i class="fas fa-sync-alt me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="movements-timeline">
                            @foreach($recentMovements as $movement)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $movement->type === 'IN' ? 'marker-in' : 'marker-out' }}">
                                    <i class="fas fa-{{ $movement->type === 'IN' ? 'arrow-down' : 'arrow-up' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="timeline-title">
                                            <a href="{{ route('inventory.show', $movement->inventoryItem) }}">
                                                {{ $movement->inventoryItem->name }}
                                            </a>
                                        </h6>
                                        <span class="timeline-time">{{ $movement->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <span class="movement-type {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                            {{ $movement->type === 'IN' ? 'Entrada' : 'Salida' }}:
                                            <strong>{{ $movement->quantity }} {{ $movement->inventoryItem->unit_measure }}</strong>
                                        </span>
                                        <div class="movement-details">
                                            <span class="movement-reason">
                                                @switch($movement->reason)
                                                    @case('PURCHASE')
                                                        <i class="fas fa-shopping-cart"></i> Compra
                                                        @break
                                                    @case('SALE')
                                                        <i class="fas fa-hand-holding-usd"></i> Venta
                                                        @break
                                                    @case('ADJUSTMENT')
                                                        <i class="fas fa-cogs"></i> Ajuste
                                                        @break
                                                    @case('INITIAL_STOCK')
                                                        <i class="fas fa-warehouse"></i> Stock Inicial
                                                        @break
                                                    @default
                                                        <i class="fas fa-circle"></i> {{ ucfirst($movement->reason) }}
                                                @endswitch
                                            </span>
                                            @if($movement->user)
                                                <span class="movement-user">por {{ $movement->user->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View mode toggle functionality
    const countViewRadio = document.getElementById('countView');
    const valueViewRadio = document.getElementById('valueView');
    
    function updateProgressBars(showCount = false) {
        const progressBars = document.querySelectorAll('.progress-bar');
        const percentageLabels = document.querySelectorAll('.progress-percentage');
        const countLabels = document.querySelectorAll('.progress-count');
        
        progressBars.forEach((bar, index) => {
            const width = showCount ? bar.getAttribute('data-count-width') : bar.getAttribute('data-value-width');
            bar.style.width = width;
        });
        
        percentageLabels.forEach(label => {
            label.style.display = showCount ? 'none' : 'inline';
        });
        
        countLabels.forEach(label => {
            label.style.display = showCount ? 'inline' : 'none';
        });
    }
    
    if (countViewRadio && valueViewRadio) {
        countViewRadio.addEventListener('change', () => updateProgressBars(true));
        valueViewRadio.addEventListener('change', () => updateProgressBars(false));
    }
});

function showReportOptions() {
    const options = [
        'Reporte completo de inventario',
        'Productos con stock bajo',
        'Movimientos por período',
        'Análisis por categorías'
    ];
    
    let optionsList = options.map((option, index) => `${index + 1}. ${option}`).join('\n');
    
    showGlobalAlert('Opciones de reportes disponibles:\n\n' + optionsList + '\n\nFuncionalidad en desarrollo...', 'info');
}

function refreshMovements() {
    showGlobalAlert('Actualizando actividad reciente...', 'info');
    // Simulate refresh
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}
</script>
@endpush

@push('styles')
<style>
/* Summary Cards */
.summary-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
    height: 100%;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.summary-card:hover::before {
    opacity: 1;
}

.summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1.25rem;
    flex-shrink: 0;
}

.total-products .summary-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.inventory-value .summary-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.low-stock .summary-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.out-stock .summary-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.summary-content {
    flex: 1;
    min-width: 0;
}

.summary-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.summary-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.summary-detail {
    font-size: 0.875rem;
    color: #9ca3af;
}

.summary-trend {
    font-size: 1.25rem;
    margin-left: 1rem;
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.category-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.category-card:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.category-name h6 {
    margin: 0;
    color: #374151;
    font-weight: 600;
}

.category-products {
    font-size: 0.875rem;
    color: #6b7280;
}

.category-value {
    color: #059669;
    font-weight: 600;
    font-size: 1.125rem;
}

.category-progress .progress {
    height: 8px;
    background-color: #e5e7eb;
}

.category-progress .progress-bar {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #6b7280;
}

/* Quick Actions */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
}

.quick-action-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
    color: inherit;
    text-decoration: none;
}

.qa-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    margin-bottom: 0.75rem;
}

.qa-content h6 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.qa-content p {
    margin: 0;
    font-size: 0.75rem;
    color: #6b7280;
}

/* Alerts */
.alert {
    border: none;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
}

.alert-icon {
    font-size: 1.25rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.alert-content {
    flex: 1;
    min-width: 0;
}

.alert-link {
    color: inherit;
    text-decoration: underline;
    font-weight: 600;
}

.alert-link:hover {
    color: inherit;
    text-decoration: none;
}

/* Stats Mini */
.stats-mini {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.stat-mini-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.stat-mini-item:last-child {
    border-bottom: none;
}

.stat-mini-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.stat-mini-value {
    font-weight: 600;
    color: #374151;
}

/* Timeline */
.movements-timeline {
    position: relative;
}

.movements-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    display: flex;
    margin-bottom: 1.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    margin-right: 1rem;
    flex-shrink: 0;
    z-index: 1;
    border: 2px solid #ffffff;
}

.marker-in {
    background-color: #10b981;
    color: white;
}

.marker-out {
    background-color: #ef4444;
    color: white;
}

.timeline-content {
    flex: 1;
    min-width: 0;
    padding-bottom: 0.5rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.timeline-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.timeline-title a {
    color: #374151;
    text-decoration: none;
}

.timeline-title a:hover {
    color: #3b82f6;
    text-decoration: underline;
}

.timeline-time {
    font-size: 0.75rem;
    color: #9ca3af;
    white-space: nowrap;
}

.timeline-body {
    font-size: 0.875rem;
}

.movement-type {
    font-weight: 500;
    display: block;
    margin-bottom: 0.25rem;
}

.movement-details {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.movement-reason, .movement-user {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
}

.empty-icon {
    font-size: 3rem;
    color: #9ca3af;
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

/* Responsive Design */
@media (max-width: 768px) {
    .summary-card {
        flex-direction: column;
        text-align: center;
        padding: 1.25rem;
    }
    
    .summary-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .summary-trend {
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .category-header {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .movement-details {
        flex-direction: column;
        gap: 0.25rem;
    }
}

@media (max-width: 576px) {
    .summary-number {
        font-size: 1.5rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .movements-timeline::before {
        left: 8px;
    }
    
    .timeline-marker {
        width: 24px;
        height: 24px;
        font-size: 0.75rem;
    }
}

/* Animation Classes */
.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 1rem 0; }
    100% { background-position: 0 0; }
}

/* Color Variants */
.bg-primary { background-color: #3b82f6 !important; }
.bg-warning { background-color: #f59e0b !important; }
.bg-info { background-color: #06b6d4 !important; }
.bg-secondary { background-color: #6b7280 !important; }
</style>
@endpush