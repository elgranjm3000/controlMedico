@extends('layouts.app')

@section('title', 'Nuevo Gasto')

@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Registrar Nuevo Gasto</h2>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('expenses.index') }}">Gastos</a>
                        </li>
                        <li class="breadcrumb-item active">Nuevo Gasto</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Expense Form -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-receipt widget-icon"></i>
                            <h5>Información del Nuevo Gasto</h5>
                            <span class="widget-subtitle">Complete todos los campos requeridos</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" action="{{ route('expenses.store') }}" data-loading id="expenseForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-building me-2"></i>Información del Proveedor</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="supplier_name" class="form-label required">Nombre del Proveedor</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-building"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('supplier_name') is-invalid @enderror" 
                                                            id="supplier_name" 
                                                            name="supplier_name" 
                                                            value="{{ old('supplier_name') }}" 
                                                            required
                                                            placeholder="Nombre de la empresa o proveedor">
                                                    </div>
                                                    @error('supplier_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="invoice_number" class="form-label">Número de Factura</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('invoice_number') is-invalid @enderror" 
                                                            id="invoice_number" 
                                                            name="invoice_number" 
                                                            value="{{ old('invoice_number') }}"
                                                            placeholder="Número de factura (opcional)">
                                                    </div>
                                                    @error('invoice_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount and Category -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-tags me-2"></i>Categoría y Monto</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="category" class="form-label required">Categoría</label>
                                                    <select class="form-select @error('category') is-invalid @enderror" 
                                                            id="category" 
                                                            name="category" 
                                                            required>
                                                        <option value="">Seleccionar categoría</option>
                                                        <option value="servicios" {{ old('category') == 'servicios' ? 'selected' : '' }}>
                                                            Servicios
                                                        </option>
                                                        <option value="nomina" {{ old('category') == 'nomina' ? 'selected' : '' }}>
                                                            Nómina
                                                        </option>
                                                        <option value="honorarios_medicos" {{ old('category') == 'honorarios_medicos' ? 'selected' : '' }}>
                                                            Honorarios Médicos
                                                        </option>
                                                        <option value="insumos" {{ old('category') == 'insumos' ? 'selected' : '' }}>
                                                            Insumos
                                                        </option>
                                                        <option value="equipo" {{ old('category') == 'equipo' ? 'selected' : '' }}>
                                                            Equipo
                                                        </option>
                                                        <option value="otros" {{ old('category') == 'otros' ? 'selected' : '' }}>
                                                            Otros
                                                        </option>
                                                    </select>
                                                    @error('category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="amount" class="form-label required">Monto</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </span>
                                                        <input type="number" 
                                                            class="form-control @error('amount') is-invalid @enderror" 
                                                            id="amount" 
                                                            name="amount" 
                                                            value="{{ old('amount') }}" 
                                                            required
                                                            min="0.01"
                                                            step="0.01"
                                                            placeholder="0.00">
                                                    </div>
                                                    @error('amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-file-alt me-2"></i>Descripción del Gasto</h6>
                                        </div>
                                        <div class="form-group">
                                            <label for="description" class="form-label required">Descripción</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="4"
                                                      required
                                                      placeholder="Describa detalladamente en qué se gastó el dinero...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Incluya información específica sobre el gasto para facilitar el seguimiento contable
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date and Payment Method -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-calendar me-2"></i>Fecha del Gasto</h6>
                                        </div>
                                        <div class="form-group">
                                            <label for="expense_date" class="form-label required">Fecha</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="date" 
                                                    class="form-control @error('expense_date') is-invalid @enderror" 
                                                    id="expense_date" 
                                                    name="expense_date" 
                                                    value="{{ old('expense_date', date('Y-m-d')) }}" 
                                                    required
                                                    max="{{ date('Y-m-d') }}">
                                            </div>
                                            @error('expense_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-credit-card me-2"></i>Método de Pago</h6>
                                        </div>
                                        <div class="form-group">
                                            <label for="payment_method" class="form-label required">Método de Pago</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" 
                                                    name="payment_method" 
                                                    required>
                                                <option value="">Seleccionar método</option>
                                                <option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>
                                                    Efectivo
                                                </option>
                                                <option value="transferencia" {{ old('payment_method') == 'transferencia' ? 'selected' : '' }}>
                                                    Transferencia Bancaria
                                                </option>
                                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>
                                                    Cheque
                                                </option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-summary">
                                        <div class="form-actions">
                                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Cancelar
                                            </a>
                                            <button type="button" class="btn btn-info" onclick="resetForm()">
                                                <i class="fas fa-undo me-1"></i>
                                                Limpiar Formulario
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-save me-1"></i>
                                                Registrar Gasto
                                            </button>
                                        </div>
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
    const form = document.getElementById('expenseForm');
    const amountInput = document.getElementById('amount');
    const supplierInput = document.getElementById('supplier_name');
    const categorySelect = document.getElementById('category');

    // Format amount input
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            // Remove any non-numeric characters except decimal point
            let value = this.value.replace(/[^0-9.]/g, '');
            
            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Limit to 2 decimal places
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            
            this.value = value;
            validateField(this);
        });
    }

    // Validate supplier name
    if (supplierInput) {
        supplierInput.addEventListener('input', function() {
            validateField(this);
        });
    }

    // Category change handler
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            validateField(this);
            updatePlaceholderText();
        });
    }

    function updatePlaceholderText() {
        const descriptionField = document.getElementById('description');
        const category = categorySelect.value;
        
        const placeholders = {
            'servicios': 'Ej: Servicio de limpieza, mantenimiento, consultoría...',
            'nomina': 'Ej: Salario mensual, bonificaciones, prestaciones...',
            'honorarios_medicos': 'Ej: Honorarios por consultas, procedimientos...',
            'insumos': 'Ej: Medicamentos, material médico, suministros...',
            'equipo': 'Ej: Compra o mantenimiento de equipos médicos...',
            'otros': 'Describa detalladamente el gasto realizado...'
        };
        
        if (descriptionField && placeholders[category]) {
            descriptionField.placeholder = placeholders[category];
        }
    }

    function validateField(field) {
        const value = field.value.trim();
        field.classList.remove('is-valid', 'is-invalid');
        
        let isValid = true;
        
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        } else if (field.type === 'number' && value) {
            const numValue = parseFloat(value);
            isValid = numValue > 0;
        }
        
        field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        return isValid;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        let isFormValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            e.preventDefault();
            showToast('Por favor complete todos los campos requeridos correctamente', 'error');
            return;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Registrando...';
            
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        }
    });
});

function resetForm() {
    if (confirm('¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.')) {
        document.getElementById('expenseForm').reset();
        document.querySelectorAll('.form-control, .form-select').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        // Reset date to today
        document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
        showToast('Formulario limpiado', 'info');
    }
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    const iconMap = {
        'success': 'check-circle',
        'error': 'times-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    
    toast.className = `toast ${type} show`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${iconMap[type]} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush