@extends('layouts.app')
@section('title', 'Remover Stock - ' . $inventoryItem->name)
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
                        <li class="breadcrumb-item active">Remover Stock</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-minus-circle widget-icon"></i>
                            <h5>Remover Stock</h5>
                            <span class="widget-subtitle">{{ $inventoryItem->name }} ({{ $inventoryItem->code }})</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        @if($inventoryItem->current_stock == 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Sin Stock Disponible</strong><br>
                                Este producto no tiene stock disponible para remover.
                            </div>
                            <div class="form-actions mt-4">
                                <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver al Producto
                                </a>
                                <a href="{{ route('inventory.showAddStock', $inventoryItem) }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Agregar Stock
                                </a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('inventory.removeStock', $inventoryItem) }}" data-loading>
                                @csrf
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="quantity" class="form-label required">Cantidad a Remover</label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control @error('quantity') is-invalid @enderror" 
                                                   id="quantity" name="quantity" 
                                                   value="{{ old('quantity', 1) }}" 
                                                   min="1" max="{{ $inventoryItem->current_stock }}" required>
                                            <span class="input-group-text">{{ $inventoryItem->unit_measure }}</span>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Máximo disponible: <strong>{{ $inventoryItem->current_stock }} {{ $inventoryItem->unit_measure }}</strong>
                                        </small>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="reason" class="form-label required">Motivo</label>
                                        <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                                            <option value="">Seleccionar motivo</option>
                                            <option value="SALE" {{ old('reason') == 'SALE' ? 'selected' : '' }}>Venta</option>
                                            <option value="ADJUSTMENT" {{ old('reason') == 'ADJUSTMENT' ? 'selected' : '' }}>Ajuste de Inventario</option>
                                            <option value="DAMAGE" {{ old('reason') == 'DAMAGE' ? 'selected' : '' }}>Producto Dañado</option>
                                            <option value="EXPIRED" {{ old('reason') == 'EXPIRED' ? 'selected' : '' }}>Producto Vencido</option>
                                            <option value="THEFT" {{ old('reason') == 'THEFT' ? 'selected' : '' }}>Robo/Pérdida</option>
                                        </select>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="notes" class="form-label">Notas</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" 
                                                  rows="3" 
                                                  placeholder="Notas adicionales sobre este movimiento (opcional)">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-actions mt-4">
                                    <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-minus me-1"></i>Remover Stock
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-info-circle widget-icon"></i>
                            <h5>Información Actual</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="info-item">
                            <strong>Stock Actual:</strong>
                            <span class="text-primary fs-5">{{ $inventoryItem->current_stock }} {{ $inventoryItem->unit_measure }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Stock Mínimo:</strong>
                            <span class="{{ $inventoryItem->current_stock <= $inventoryItem->minimum_stock ? 'text-warning' : '' }}">
                                {{ $inventoryItem->minimum_stock }} {{ $inventoryItem->unit_measure }}
                            </span>
                        </div>
                        <div class="info-item">
                            <strong>Precio Unitario:</strong>
                            <span class="text-success">${{ number_format($inventoryItem->unit_price, 2) }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Valor Total Actual:</strong>
                            <span class="text-success">${{ number_format($inventoryItem->current_stock * $inventoryItem->unit_price, 2) }}</span>
                        </div>
                        
                        @if($inventoryItem->current_stock <= $inventoryItem->minimum_stock)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡Atención!</strong><br>
                            El stock actual ya está en el límite mínimo.
                        </div>
                        @endif
                        
                        @if($inventoryItem->current_stock > 0)
                        <hr>
                        <h6><i class="fas fa-eye me-2"></i>Vista Previa</h6>
                        <div id="preview-info" style="display: none;">
                            <div class="alert alert-info">
                                <div class="info-item">
                                    <strong>Nuevo Stock:</strong>
                                    <span id="new-stock">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Nuevo Valor Total:</strong>
                                    <span id="new-total-value">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Disminución:</strong>
                                    <span class="text-warning" id="decrease">-</span>
                                </div>
                            </div>
                            <div id="low-stock-warning" class="alert alert-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>¡Advertencia!</strong><br>
                                El stock resultante estará por debajo del mínimo requerido.
                            </div>
                            <div id="zero-stock-warning" class="alert alert-danger" style="display: none;">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>¡Crítico!</strong><br>
                                El producto quedará sin stock.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Tips -->
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-lightbulb widget-icon"></i>
                            <h5>Consejos</h5>
                        </div>
                    </div>
                    <div class="widget-body">
                        <small class="text-muted">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <strong>Venta:</strong> Use cuando se venda el producto a un cliente.<br><br>
                            <i class="fas fa-wrench me-2"></i>
                            <strong>Ajuste:</strong> Para corregir diferencias encontradas.<br><br>
                            <i class="fas fa-broken-image me-2"></i>
                            <strong>Dañado:</strong> Cuando el producto está en mal estado.<br><br>
                            <i class="fas fa-calendar-times me-2"></i>
                            <strong>Vencido:</strong> Para productos que expiraron.<br><br>
                            <i class="fas fa-user-secret me-2"></i>
                            <strong>Robo/Pérdida:</strong> Por situaciones imprevistas.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const currentStock = {{ $inventoryItem->current_stock }};
    const minimumStock = {{ $inventoryItem->minimum_stock }};
    const unitPrice = {{ $inventoryItem->unit_price }};
    
    function updatePreview() {
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (quantity > 0 && quantity <= currentStock) {
            const newStock = currentStock - quantity;
            const newTotalValue = newStock * unitPrice;
            const decrease = quantity * unitPrice;
            
            // Update preview values
            document.getElementById('new-stock').textContent = `${newStock} {{ $inventoryItem->unit_measure }}`;
            document.getElementById('new-stock').className = newStock <= minimumStock ? 'text-warning' : 'text-primary';
            document.getElementById('new-total-value').textContent = `$${newTotalValue.toFixed(2)}`;
            document.getElementById('decrease').textContent = `-$${decrease.toFixed(2)}`;
            
            // Show/hide warnings
            const lowStockWarning = document.getElementById('low-stock-warning');
            const zeroStockWarning = document.getElementById('zero-stock-warning');
            
            if (newStock === 0) {
                zeroStockWarning.style.display = 'block';
                lowStockWarning.style.display = 'none';
            } else if (newStock <= minimumStock) {
                lowStockWarning.style.display = 'block';
                zeroStockWarning.style.display = 'none';
            } else {
                lowStockWarning.style.display = 'none';
                zeroStockWarning.style.display = 'none';
            }
            
            document.getElementById('preview-info').style.display = 'block';
        } else {
            document.getElementById('preview-info').style.display = 'none';
        }
    }
    
    // Only add event listener if the input exists (not when stock is 0)
    if (quantityInput) {
        quantityInput.addEventListener('input', updatePreview);
        
        // Validate max quantity
        quantityInput.addEventListener('blur', function() {
            const quantity = parseInt(this.value) || 0;
            if (quantity > currentStock) {
                this.value = currentStock;
                showGlobalAlert(`La cantidad máxima disponible es ${currentStock}`, 'warning');
                updatePreview();
            }
        });
        
        // Initial preview update
        updatePreview();
    }
});
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: #ef4444;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding: 1.5rem 0;
    border-top: 1px solid #e5e7eb;
}

.info-item {
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-item:last-child {
    margin-bottom: 0;
}

.fs-5 {
    font-size: 1.25rem;
    font-weight: 600;
}

/* Warning states */
.alert-warning .info-item strong,
.alert-danger .info-item strong {
    color: inherit;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .info-item span {
        margin-top: 0.25rem;
    }
}
</style>
@endpush