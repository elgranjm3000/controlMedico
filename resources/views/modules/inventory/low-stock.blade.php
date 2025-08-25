@extends('layouts.app')
@section('title', 'Productos con Stock Bajo')
@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Productos con Stock Bajo</h2>
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
                        <li class="breadcrumb-item active">Stock Bajo</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Alert Banner -->
        @if($count > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-start border-warning border-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="alert-icon me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="alert-content flex-grow-1">
                            <h5 class="alert-heading mb-1">¡Atención Requerida!</h5>
                            <p class="mb-0">
                                Hay <strong>{{ $count }}</strong> producto(s) con stock igual o menor al mínimo configurado. 
                                Se recomienda reabastecer estos productos lo antes posible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-exclamation-triangle widget-icon text-warning"></i>
                            <h5>Lista de Productos con Stock Bajo</h5>
                            <span class="widget-subtitle">
                                {{ $count }} producto(s) requieren atención inmediata
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Inventario
                            </a>
                            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Nuevo Producto
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        @if($items->count() > 0)
                            <!-- Bulk Actions -->
                            <div class="bulk-actions mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">
                                                Seleccionar todos
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="btn-group" id="bulkActionsGroup" style="display: none;">
                                            <button type="button" class="btn btn-success btn-sm" onclick="bulkAddStock()">
                                                <i class="fas fa-plus me-1"></i>Agregar Stock Masivo
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportSelected()">
                                                <i class="fas fa-download me-1"></i>Exportar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="lowStockTable">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="headerCheckbox">
                                            </th>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th class="text-center">Stock Actual</th>
                                            <th class="text-center">Stock Mínimo</th>
                                            <th class="text-center">Déficit</th>
                                            <th class="text-center">Prioridad</th>
                                            <th class="text-end">Valor Unit.</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        @php
                                            $deficit = max(0, $item->minimum_stock - $item->current_stock);
                                            $priority = $item->current_stock == 0 ? 'critical' : ($item->current_stock <= ($item->minimum_stock / 2) ? 'high' : 'medium');
                                        @endphp
                                        <tr class="low-stock-row {{ $item->current_stock == 0 ? 'table-danger' : 'table-warning' }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $item->code }}</strong>
                                                @if($item->current_stock == 0)
                                                    <div class="badge bg-danger">SIN STOCK</div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('inventory.show', $item) }}" class="contact-link">
                                                    <strong>{{ $item->name }}</strong>
                                                </a>
                                                @if($item->description)
                                                    <small class="text-muted d-block">{{ Str::limit($item->description, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ ucfirst($item->category) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="stock-badge {{ $item->current_stock == 0 ? 'stock-zero' : 'stock-low' }}">
                                                    {{ $item->current_stock }}
                                                </span>
                                                <small class="text-muted d-block">{{ $item->unit_measure }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-info fw-bold">{{ $item->minimum_stock }}</span>
                                                <small class="text-muted d-block">{{ $item->unit_measure }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="deficit-badge">
                                                    {{ $deficit }} {{ $item->unit_measure }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @switch($priority)
                                                    @case('critical')
                                                        <span class="priority-badge priority-critical">
                                                            <i class="fas fa-exclamation-circle"></i> CRÍTICO
                                                        </span>
                                                        @break
                                                    @case('high')
                                                        <span class="priority-badge priority-high">
                                                            <i class="fas fa-exclamation-triangle"></i> ALTO
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="priority-badge priority-medium">
                                                            <i class="fas fa-minus-circle"></i> MEDIO
                                                        </span>
                                                @endswitch
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">${{ number_format($item->unit_price, 2) }}</strong>
                                                <small class="text-muted d-block">
                                                    Valor: ${{ number_format($item->current_stock * $item->unit_price, 2) }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('inventory.show', $item) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('inventory.showAddStock', $item) }}" 
                                                       class="btn btn-success btn-sm" 
                                                       title="Agregar Stock">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                data-bs-toggle="dropdown" 
                                                                title="Más opciones">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('inventory.edit', $item) }}">
                                                                    <i class="fas fa-edit me-2"></i>Editar Producto
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('inventory.movements', $item) }}">
                                                                    <i class="fas fa-history me-2"></i>Ver Movimientos
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <button class="dropdown-item" onclick="quickAddStock('{{ $item->id }}', '{{ $item->name }}', {{ $deficit }})">
                                                                    <i class="fas fa-bolt me-2"></i>Agregar Stock Rápido
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
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
                                        Mostrando {{ $items->firstItem() }} a {{ $items->lastItem() }} 
                                        de {{ $items->total() }} productos con stock bajo
                                    </small>
                                </div>
                                {{ $items->links() }}
                            </div>
                        @else
                            <div class="empty-state success-state">
                                <div class="empty-icon text-success">
                                    <i class="fas fa-check-circle fa-4x"></i>
                                </div>
                                <h4 class="text-success mb-3">¡Excelente!</h4>
                                <h6>Todos los productos tienen stock suficiente</h6>
                                <p>No hay productos que requieran atención por stock bajo en este momento.</p>
                                <div class="empty-actions">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                                        <i class="fas fa-boxes me-1"></i>Ver Todo el Inventario
                                    </a>
                                    <a href="{{ route('inventory.summary') }}" class="btn btn-outline-info">
                                        <i class="fas fa-chart-pie me-1"></i>Ver Resumen
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

<!-- Quick Add Stock Modal -->
<div class="modal fade" id="quickAddStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bolt me-2"></i>Agregar Stock Rápido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickAddForm">
                    <input type="hidden" id="quickItemId">
                    <div class="mb-3">
                        <label class="form-label">Producto:</label>
                        <p class="fw-bold" id="quickItemName"></p>
                    </div>
                    <div class="mb-3">
                        <label for="quickQuantity" class="form-label">Cantidad Sugerida:</label>
                        <input type="number" class="form-control" id="quickQuantity" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="quickReason" class="form-label">Motivo:</label>
                        <select class="form-select" id="quickReason">
                            <option value="PURCHASE">Compra</option>
                            <option value="ADJUSTMENT">Ajuste</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="processQuickAdd()">
                    <i class="fas fa-plus me-1"></i>Agregar Stock
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionsGroup = document.getElementById('bulkActionsGroup');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActionsGroup.style.display = 'block';
        } else {
            bulkActionsGroup.style.display = 'none';
        }
    }

    // Header checkbox functionality
    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Row checkboxes functionality
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            
            // Update header checkbox state
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            
            if (headerCheckbox) {
                headerCheckbox.checked = allChecked;
                headerCheckbox.indeterminate = anyChecked && !allChecked;
            }
        });
    });

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            if (headerCheckbox) {
                headerCheckbox.checked = this.checked;
                headerCheckbox.indeterminate = false;
            }
            updateBulkActions();
        });
    }
});

function quickAddStock(itemId, itemName, suggestedQuantity) {
    document.getElementById('quickItemId').value = itemId;
    document.getElementById('quickItemName').textContent = itemName;
    document.getElementById('quickQuantity').value = suggestedQuantity;
    
    const modal = new bootstrap.Modal(document.getElementById('quickAddStockModal'));
    modal.show();
}

function processQuickAdd() {
    const itemId = document.getElementById('quickItemId').value;
    const quantity = document.getElementById('quickQuantity').value;
    const reason = document.getElementById('quickReason').value;
    
    if (!quantity || quantity < 1) {
        showGlobalAlert('Por favor ingrese una cantidad válida', 'error');
        return;
    }
    
    // Redirect to add stock page with pre-filled data
    const url = `/inventory/${itemId}/add-stock?quantity=${quantity}&reason=${reason}`;
    window.location.href = url;
}

function bulkAddStock() {
    const selectedItems = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                               .map(cb => cb.value);
    
    if (selectedItems.length === 0) {
        showGlobalAlert('Por favor seleccione al menos un producto', 'warning');
        return;
    }
    
    showGlobalAlert('Funcionalidad de stock masivo en desarrollo', 'info');
    // TODO: Implement bulk add stock functionality
}

function exportSelected() {
    const selectedItems = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                               .map(cb => cb.value);
    
    if (selectedItems.length === 0) {
        showGlobalAlert('Por favor seleccione al menos un producto', 'warning');
        return;
    }
    
    showGlobalAlert('Funcionalidad de exportación en desarrollo', 'info');
    // TODO: Implement export functionality
}
</script>
@endpush

@push('styles')
<style>
.alert-icon {
    flex-shrink: 0;
}

.alert-content {
    min-width: 0;
}

.low-stock-row {
    border-left: 4px solid #fbbf24;
}

.low-stock-row.table-danger {
    border-left-color: #ef4444;
}

.stock-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.stock-low {
    background-color: #fef3c7;
    color: #92400e;
}

.stock-zero {
    background-color: #fee2e2;
    color: #991b1b;
}

.deficit-badge {
    background-color: #fee2e2;
    color: #991b1b;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
}

.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.priority-critical {
    background-color: #dc2626;
    color: white;
}

.priority-high {
    background-color: #f59e0b;
    color: white;
}

.priority-medium {
    background-color: #6b7280;
    color: white;
}

.bulk-actions {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
}

.success-state .empty-icon {
    margin-bottom: 1.5rem;
}

.success-state h4 {
    font-weight: 600;
}

.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.results-info {
    flex: 1;
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
    
    .bulk-actions .row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .bulk-actions .text-end {
        text-align: start !important;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .empty-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .empty-actions .btn {
        width: 100%;
        max-width: 250px;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .priority-badge {
        font-size: 0.625rem;
        padding: 0.125rem 0.5rem;
    }
    
    .stock-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .deficit-badge {
        padding: 0.125rem 0.375rem;
        font-size: 0.75rem;
    }
}

/* Animation for priority badges */
.priority-critical {
    animation: pulse-critical 2s infinite;
}

@keyframes pulse-critical {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
</style>
@endpush
