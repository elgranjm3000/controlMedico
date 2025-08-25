@extends('layouts.app')
@section('title', 'Agregar Stock - ' . $inventoryItem->name)
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
                        <li class="breadcrumb-item active">Agregar Stock</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-plus-circle widget-icon"></i>
                            <h5>Agregar Stock</h5>
                            <span class="widget-subtitle">{{ $inventoryItem->name }} ({{ $inventoryItem->code }})</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" action="{{ route('inventory.addStock', $inventoryItem) }}" data-loading>
                            @csrf
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label required">Cantidad a Agregar</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('quantity') is-invalid @enderror" 
                                               id="quantity" name="quantity" 
                                               value="{{ old('quantity', 1) }}" 
                                               min="1" required>
                                        <span class="input-group-text">{{ $inventoryItem->unit_measure }}</span>
                                    </div>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="unit_price" class="form-label required">Precio Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('unit_price') is-invalid @enderror" 
                                               id="unit_price" name="unit_price" 
                                               value="{{ old('unit_price', $inventoryItem->unit_price) }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="reason" class="form-label required">Motivo</label>
                                    <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                                        <option value="">Seleccionar motivo</option>
                                        <option value="PURCHASE" {{ old('reason') == 'PURCHASE' ? 'selected' : '' }}>Compra</option>
                                        <option value="ADJUSTMENT" {{ old('reason') == 'ADJUSTMENT' ? 'selected' : '' }}>Ajuste de Inventario</option>
                                        <option value="RETURN" {{ old('reason') == 'RETURN' ? 'selected' : '' }}>Devolución de Cliente</option>
                                        <option value="PRODUCTION" {{ old('reason') == 'PRODUCTION' ? 'selected' : '' }}>Producción</option>
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
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Agregar Stock
                                </button>
                            </div>
                        </form>
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
                            <span>{{ $inventoryItem->minimum_stock }} {{ $inventoryItem->unit_measure }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Precio Actual:</strong>
                            <span class="text-success">${{ number_format($inventoryItem->unit_price, 2) }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Valor Total Actual:</strong>
                            <span class="text-success">${{ number_format($inventoryItem->current_stock * $inventoryItem->unit_price, 2) }}</span>
                        </div>
                        
                        <hr>
                        <h6><i class="fas fa-eye me-2"></i>Vista Previa</h6>
                        <div id="preview-info" style="display: none;">
                            <div class="alert alert-info">
                                <div class="info-item">
                                    <strong>Nuevo Stock:</strong>
                                    <span class="text-success" id="new-stock">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Nuevo Valor Total:</strong>
                                    <span class="text-success" id="new-total-value">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Incremento:</strong>
                                    <span class="text-info" id="increment">-</span>
                                </div>
                            </div>
                        </div>
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
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Compra:</strong> Use cuando compre mercancía nueva.<br><br>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Ajuste:</strong> Para corregir diferencias en el inventario.<br><br>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Devolución:</strong> Cuando un cliente devuelve un producto.<br><br>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Producción:</strong> Para productos fabricados internamente.
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
    const priceInput = document.getElementById('unit_price');
    const currentStock = {{ $inventoryItem->current_stock }};
    const currentPrice = {{ $inventoryItem->unit_price }};
    
    function updatePreview() {
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        
        if (quantity > 0 && price >= 0) {
            const newStock = currentStock + quantity;
            const newTotalValue = newStock * price;
            const increment = quantity * price;
            
            document.getElementById('new-stock').textContent = `${newStock} {{ $inventoryItem->unit_measure }}`;
            document.getElementById('new-total-value').textContent = `$${newTotalValue.toFixed(2)}`;
            document.getElementById('increment').textContent = `+$${increment.toFixed(2)}`;
            document.getElementById('preview-info').style.display = 'block';
        } else {
            document.getElementById('preview-info').style.display = 'none';
        }
    }
    
    quantityInput.addEventListener('input', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    
    // Initial preview update
    updatePreview();
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

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush
