@extends('layouts.app')
@section('title', isset($inventoryItem) ? 'Editar Producto' : 'Nuevo Producto')
@section('content')
<div class="main-content">
    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventario</a></li>
                        <li class="breadcrumb-item active">{{ isset($inventoryItem) ? 'Editar' : 'Nuevo' }} Producto</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-boxes widget-icon"></i>
                            <h5>{{ isset($inventoryItem) ? 'Editar' : 'Nuevo' }} Producto</h5>
                            <span class="widget-subtitle">
                                @if(!isset($inventoryItem))
                                    Complete los datos del producto
                                @else
                                    Modificar información del producto
                                @endif
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" 
                              action="{{ isset($inventoryItem) ? route('inventory.update', $inventoryItem) : route('inventory.store') }}" 
                              id="inventoryForm" data-loading>
                            @csrf
                            @if(isset($inventoryItem))
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-info-circle me-2"></i>Información Básica</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <!-- Product Name -->
                                            <div class="col-md-6">
                                                <label for="name" class="form-label required">Nombre del Producto</label>
                                                <input type="text" 
                                                       class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" 
                                                       value="{{ old('name', $inventoryItem->name ?? '') }}" 
                                                       required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Product Code -->
                                            <div class="col-md-6">
                                                <label for="code" class="form-label required">Código del Producto</label>
                                                <input type="text" 
                                                       class="form-control @error('code') is-invalid @enderror" 
                                                       id="code" name="code" 
                                                       value="{{ old('code', $inventoryItem->code ?? '') }}" 
                                                       required>
                                                <small class="form-text text-muted">Código único identificador</small>
                                                @error('code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Description -->
                                            <div class="col-12">
                                                <label for="description" class="form-label">Descripción</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                                          id="description" name="description" 
                                                          rows="3" 
                                                          placeholder="Descripción detallada del producto">{{ old('description', $inventoryItem->description ?? '') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category and Unit -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-tags me-2"></i>Categorización</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <!-- Category -->
                                            <div class="col-md-6">
                                                <label for="category" class="form-label required">Categoría</label>
                                                <div class="input-group">
                                                    <select id="category" name="category" class="form-control">
                                                        <option value="medicamento">Medicamento</option>
                                                        <option value="insumo_medico">Insumo médico</option>
                                                        <option value="material_oficina">Material de oficina</option>
                                                        <option value="equipo">Equipo</option>
                                                    </select>
                                                </div>
                                                @error('category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Unit of Measure -->
                                            <div class="col-md-6">
                                                <label for="unit_measure" class="form-label required">Unidad de Medida</label>
                                                <select class="form-select @error('unit_measure') is-invalid @enderror" 
                                                        id="unit_measure" name="unit_measure" required>
                                                    <option value="">Seleccionar unidad</option>
                                                    <option value="piezas" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'piezas' ? 'selected' : '' }}>Piezas</option>
                                                    <option value="cajas" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'cajas' ? 'selected' : '' }}>Cajas</option>
                                                    <option value="litros" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'litros' ? 'selected' : '' }}>Litros</option>
                                                    <option value="kilogramos" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'kilogramos' ? 'selected' : '' }}>Kilogramos</option>
                                                    <option value="metros" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'metros' ? 'selected' : '' }}>Metros</option>
                                                    <option value="frascos" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'frascos' ? 'selected' : '' }}>Frascos</option>
                                                    <option value="sobres" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'sobres' ? 'selected' : '' }}>Sobres</option>
                                                    <option value="tabletas" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'tabletas' ? 'selected' : '' }}>Tabletas</option>
                                                    <option value="ampolletas" {{ old('unit_measure', $inventoryItem->unit_measure ?? '') == 'ampolletas' ? 'selected' : '' }}>Ampolletas</option>
                                                </select>
                                                @error('unit_measure')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing and Stock -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-calculator me-2"></i>Precios y Stock</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <!-- Unit Price -->
                                            <div class="col-md-4">
                                                <label for="unit_price" class="form-label required">Precio Unitario</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" 
                                                           class="form-control @error('unit_price') is-invalid @enderror" 
                                                           id="unit_price" name="unit_price" 
                                                           value="{{ old('unit_price', $inventoryItem->unit_price ?? '') }}" 
                                                           step="0.01" min="0" required>
                                                </div>
                                                @error('unit_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Current Stock (only for new items) -->
                                            @if(!isset($inventoryItem))
                                            <div class="col-md-4">
                                                <label for="current_stock" class="form-label required">Stock Inicial</label>
                                                <input type="number" 
                                                       class="form-control @error('current_stock') is-invalid @enderror" 
                                                       id="current_stock" name="current_stock" 
                                                       value="{{ old('current_stock', 0) }}" 
                                                       min="0" required>
                                                <small class="form-text text-muted">Cantidad inicial en inventario</small>
                                                @error('current_stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @endif

                                            <!-- Minimum Stock -->
                                            <div class="col-md-4">
                                                <label for="minimum_stock" class="form-label required">Stock Mínimo</label>
                                                <input type="number" 
                                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                                       id="minimum_stock" name="minimum_stock" 
                                                       value="{{ old('minimum_stock', $inventoryItem->minimum_stock ?? 5) }}" 
                                                       min="0" required>
                                                <small class="form-text text-muted">Alerta cuando stock sea menor</small>
                                                @error('minimum_stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-toggle-on me-2"></i>Estado del Producto</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="is_active" 
                                                           name="is_active" 
                                                           value="1" 
                                                           {{ old('is_active', $inventoryItem->is_active ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        Producto Activo
                                                    </label>
                                                    <small class="form-text text-muted d-block">
                                                        Solo los productos activos aparecen en las facturas
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-actions">
                                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancelar</a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i>
                                            {{ isset($inventoryItem) ? 'Actualizar' : 'Crear' }} Producto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
    // Auto-generate code based on name and category
    const nameInput = document.getElementById('name');
    const categoryInput = document.getElementById('category');
    const codeInput = document.getElementById('code');
    
    // Only auto-generate for new items
    @if(!isset($inventoryItem))
    function generateCode() {
        const name = nameInput.value.trim();
        const category = categoryInput.value.trim();
        
        if (name && category) {
            const namePrefix = name.substring(0, 3).toUpperCase();
            const categoryPrefix = category.substring(0, 2).toUpperCase();
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const suggestedCode = `${categoryPrefix}-${namePrefix}-${randomNum}`;
            
            if (!codeInput.value) {
                codeInput.value = suggestedCode;
            }
        }
    }
    
    nameInput.addEventListener('blur', generateCode);
    categoryInput.addEventListener('blur', generateCode);
    @endif
    
    // Form validation
    const form = document.getElementById('inventoryForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Clear previous validation states
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        // Required field validation
        const requiredFields = ['name', 'code', 'category', 'unit_measure', 'unit_price', 'minimum_stock'];
        @if(!isset($inventoryItem))
        requiredFields.push('current_stock');
        @endif
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            }
        });
        
        // Price validation
        const unitPrice = parseFloat(document.getElementById('unit_price').value);
        if (unitPrice < 0) {
            document.getElementById('unit_price').classList.add('is-invalid');
            isValid = false;
        }
        
        // Stock validation
        @if(!isset($inventoryItem))
        const currentStock = parseInt(document.getElementById('current_stock').value);
        if (currentStock < 0) {
            document.getElementById('current_stock').classList.add('is-invalid');
            isValid = false;
        }
        @endif
        
        const minimumStock = parseInt(document.getElementById('minimum_stock').value);
        if (minimumStock < 0) {
            document.getElementById('minimum_stock').classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            showGlobalAlert('Por favor, complete todos los campos requeridos correctamente', 'error');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-section {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-section-header {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.form-section-header h6 {
    color: #374151;
    font-weight: 600;
    margin: 0;
}

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

.input-group-text {
    background-color: #f9fafb;
    border-color: #d1d5db;
    color: #6b7280;
    font-weight: 500;
}

.form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.form-text {
    font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .form-section {
        padding: 1rem;
    }
}
</style>
@endpush
