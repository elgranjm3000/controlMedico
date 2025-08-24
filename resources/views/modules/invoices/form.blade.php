@extends('layouts.app')
@section('title', isset($invoice) ? 'Editar Factura' : 'Nueva Factura')
@section('content')
<div class="main-content">
    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                        <li class="breadcrumb-item active">{{ isset($invoice) ? 'Editar' : 'Nueva' }} Factura</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-file-invoice-dollar widget-icon"></i>
                            <h5>{{ isset($invoice) ? 'Editar' : 'Nueva' }} Factura</h5>
                            <span class="widget-subtitle">
                                @if(!isset($invoice))
                                    Número sugerido: {{ $nextNumber }}
                                @endif
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" 
                              action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" 
                              id="invoiceForm">
                            @csrf
                            @if(isset($invoice))
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <!-- Patient Selection -->
                                <div class="col-md-6">
                                    <label for="patient_id" class="form-label required">Paciente</label>
                                    <select class="form-select @error('patient_id') is-invalid @enderror" 
                                            id="patient_id" name="patient_id" required>
                                        <option value="">Seleccionar paciente</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" 
                                                {{ old('patient_id', $selectedPatient->id ?? $invoice->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->getFullNameAttribute() }} - {{ $patient->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Payment Method -->
                                <div class="col-md-6">
                                    <label for="payment_method" class="form-label required">Método de Pago</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                        <option value="">Seleccionar método</option>
<option value="efectivo" {{ old('payment_method', $invoice->payment_method ?? '') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="transferencia" {{ old('payment_method', $invoice->payment_method ?? '') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="credito" {{ old('payment_method', $invoice->payment_method ?? '') == 'credito' ? 'selected' : '' }}>Crédito</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Services and Products -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-list me-2"></i>Servicios y Productos</h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                                                <i class="fas fa-plus me-1"></i>Agregar Item
                                            </button>
                                        </div>
                                        <div id="invoice-items">
                                            @if(isset($invoice) && $invoice->items->count() > 0)
                                                @foreach($invoice->items as $index => $item)
                                                    <div class="invoice-item mb-3" data-index="{{ $index }}">
                                                        <div class="row g-3">
                                                            <div class="col-md-2">
                                                                <select name="items[{{ $index }}][type]" class="form-select item-type" required>
                                                                    <option value="service" {{ $item->service_id ? 'selected' : '' }}>Servicio</option>
                                                                    <option value="inventory" {{ $item->inventory_item_id ? 'selected' : '' }}>Producto</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select name="items[{{ $index }}][item_id]" class="form-select item-select" required>
                                                                    <option value="{{ $item->service_id ?? $item->inventory_item_id }}">
                                                                        {{ $item->service->name ?? $item->inventoryItem->name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" name="items[{{ $index }}][description]" 
                                                                       class="form-control" placeholder="Descripción" 
                                                                       value="{{ $item->description }}" required>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <input type="number" name="items[{{ $index }}][quantity]" 
                                                                       class="form-control quantity" min="1" 
                                                                       value="{{ $item->quantity }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="number" name="items[{{ $index }}][unit_price]" 
                                                                       class="form-control unit-price" step="0.01" min="0" 
                                                                       value="{{ $item->unit_price }}" required>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(this)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="invoice-item mb-3" data-index="0">
                                                    <div class="row g-3">
                                                        <div class="col-md-2">
                                                            <select name="items[0][type]" class="form-select item-type" required>
                                                                <option value="">Tipo</option>
                                                                <option value="service">Servicio</option>
                                                                <option value="inventory">Producto</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select name="items[0][item_id]" class="form-select item-select" required>
                                                                <option value="">Seleccionar...</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" name="items[0][description]" class="form-control" placeholder="Descripción" required>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <input type="number" name="items[0][quantity]" class="form-control quantity" min="1" value="1" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" name="items[0][unit_price]" class="form-control unit-price" step="0.01" min="0" required>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Totals -->
                                        <div class="row mt-4">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between">
                                                            <span>Subtotal:</span>
                                                            <span id="subtotal">$0.00</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span>IVA (16%):</span>
                                                            <span id="tax">$0.00</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between fw-bold">
                                                            <span>Total:</span>
                                                            <span id="total">$0.00</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Notas adicionales (opcional)">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-actions">
                                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i>
                                            {{ isset($invoice) ? 'Actualizar' : 'Crear' }} Factura
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
let itemIndex = {{ isset($invoice) ? $invoice->items->count() : 1 }};

// Load services and products data
const services = @json($medicalServices->map(function($service) {
                return ['id' => $service->id, 'name' => $service->name, 'price' => $service->price];
                }));

const products = @json($inventoryItems->map(function($item) {
    return ['id' => $item->id, 'name' => $item->name, 'price' => $item->unit_price];
}));

document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing items
    document.querySelectorAll('.item-type').forEach(select => {
        select.addEventListener('change', function() {
            updateItemOptions(this);
        });
    });

    // Calculate totals on load
    calculateTotals();

    // Add event listeners for quantity and price changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            calculateTotals();
        }
    });
});

function addItem() {
    const container = document.getElementById('invoice-items');
    const newItem = document.createElement('div');
    newItem.className = 'invoice-item mb-3';
    newItem.setAttribute('data-index', itemIndex);
    
    newItem.innerHTML = `
        <div class="row g-3">
            <div class="col-md-2">
                <select name="items[${itemIndex}][type]" class="form-select item-type" required>
                    <option value="">Tipo</option>
                    <option value="service">Servicio</option>
                    <option value="inventory">Producto</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="items[${itemIndex}][item_id]" class="form-select item-select" required>
                    <option value="">Seleccionar...</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Descripción" required>
            </div>
            <div class="col-md-1">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" step="0.01" min="0" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newItem);
    
    // Add event listener to new type select
    newItem.querySelector('.item-type').addEventListener('change', function() {
        updateItemOptions(this);
    });
    
    itemIndex++;
}

function removeItem(button) {
    const item = button.closest('.invoice-item');
    item.remove();
    calculateTotals();
}

function updateItemOptions(typeSelect) {
    const itemSelect = typeSelect.closest('.invoice-item').querySelector('.item-select');
    const descriptionInput = typeSelect.closest('.invoice-item').querySelector('input[name$="[description]"]');
    const priceInput = typeSelect.closest('.invoice-item').querySelector('.unit-price');
    
    itemSelect.innerHTML = '<option value="">Seleccionar...</option>';
    
    const items = typeSelect.value === 'service' ? services : products;
    
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = `${item.name} - ${parseFloat(item.price).toFixed(2)}`;
        option.setAttribute('data-price', item.price);
        option.setAttribute('data-name', item.name);
        itemSelect.appendChild(option);
    });
    
    // Auto-fill description and price when item is selected
    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            descriptionInput.value = selectedOption.getAttribute('data-name');
            priceInput.value = selectedOption.getAttribute('data-price');
            calculateTotals();
        }
    });
}

function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.invoice-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity')?.value || 0);
        const price = parseFloat(item.querySelector('.unit-price')?.value || 0);
        subtotal += quantity * price;
    });
    
    const tax = subtotal * 0.16; // 16% IVA
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = `${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `${total.toFixed(2)}`;
}
</script>
@endpush
