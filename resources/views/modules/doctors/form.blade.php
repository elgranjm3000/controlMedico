@extends('layouts.app')

@section('title', isset($doctor) ? 'Editar Doctor' : 'Nuevo Doctor')

@section('content')
<!-- Sidebar -->

<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">{{ isset($doctor) ? 'Editar Doctor' : 'Nuevo Doctor' }}</h2>
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

    <!-- Main Content Area -->
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
                            <a href="{{ route('doctors.index') }}">Doctores</a>
                        </li>
                        @if(isset($doctor))
                            <li class="breadcrumb-item">
                                <a href="{{ route('doctors.show', $doctor) }}">{{ $doctor->getFullNameAttribute() }}</a>
                            </li>
                            <li class="breadcrumb-item active">Editar</li>
                        @else
                            <li class="breadcrumb-item active">Nuevo Doctor</li>
                        @endif
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Form Steps Indicator -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="form-steps">
                    <div class="form-step active">
                        <div class="form-step-number">1</div>
                        <span>Información Personal</span>
                    </div>
                    <div class="form-step">
                        <div class="form-step-number">2</div>
                        <span>Información Profesional</span>
                    </div>
                    <div class="form-step">
                        <div class="form-step-number">3</div>
                        <span>Contacto</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Form -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-{{ isset($doctor) ? 'user-edit' : 'user-plus' }} widget-icon"></i>
                            <h5>{{ isset($doctor) ? 'Editar Información del Doctor' : 'Información del Nuevo Doctor' }}</h5>
                            <span class="widget-subtitle">
                                {{ isset($doctor) ? 'Actualice los datos del doctor' : 'Complete todos los campos requeridos' }}
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                            @if(isset($doctor))
                                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalles
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" 
                              action="{{ isset($doctor) ? route('doctors.update', $doctor) : route('doctors.store') }}" 
                              data-loading 
                              id="doctorForm">
                            @csrf
                            @if(isset($doctor))
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <!-- Step 1: Personal Information -->
                                <div class="col-12 form-step-content" data-step="1">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-user me-2"></i>Información Personal</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label required">Nombre</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-user"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('name') is-invalid @enderror" 
                                                            id="name" 
                                                            name="name" 
                                                            value="{{ old('name', $doctor->name ?? '') }}" 
                                                            required
                                                            placeholder="Ingrese el nombre"
                                                            autocomplete="given-name">
                                                    </div>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name" class="form-label required">Apellidos</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-user"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('last_name') is-invalid @enderror" 
                                                            id="last_name" 
                                                            name="last_name" 
                                                            value="{{ old('last_name', $doctor->last_name ?? '') }}" 
                                                            required
                                                            placeholder="Ingrese los apellidos"
                                                            autocomplete="family-name">
                                                    </div>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                                Siguiente: Información Profesional
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Professional Information -->
                                <div class="col-12 form-step-content d-none" data-step="2">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-stethoscope me-2"></i>Información Profesional</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="specialty" class="form-label required">Especialidad</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('specialty') is-invalid @enderror" 
                                                            id="specialty" 
                                                            name="specialty" 
                                                            value="{{ old('specialty', $doctor->specialty ?? '') }}" 
                                                            required
                                                            placeholder="Ej: Cardiología, Pediatría..."
                                                            list="specialties-list">
                                                    </div>
                                                    <datalist id="specialties-list">
                                                        <option value="Medicina General">
                                                        <option value="Cardiología">
                                                        <option value="Pediatría">
                                                        <option value="Ginecología">
                                                        <option value="Dermatología">
                                                        <option value="Neurología">
                                                        <option value="Ortopedia">
                                                        <option value="Psiquiatría">
                                                        <option value="Oftalmología">
                                                        <option value="Otorrinolaringología">
                                                    </datalist>
                                                    @error('specialty')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="license_number" class="form-label required">Número de Cédula</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-id-card"></i>
                                                        </span>
                                                        <input type="text" 
                                                            class="form-control @error('license_number') is-invalid @enderror" 
                                                            id="license_number" 
                                                            name="license_number" 
                                                            value="{{ old('license_number', $doctor->license_number ?? '') }}" 
                                                            required
                                                            placeholder="Número de cédula profesional">
                                                    </div>
                                                    @error('license_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        El número de cédula debe ser único para cada doctor
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($doctor))
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Estado del Doctor</label>
                                                        <div class="form-switch">
                                                            <input class="form-check-input" 
                                                                type="checkbox" 
                                                                role="switch" 
                                                                id="is_active" 
                                                                name="is_active" 
                                                                value="1" 
                                                                {{ old('is_active', $doctor->is_active) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active">
                                                                Doctor Activo
                                                            </label>
                                                        </div>
                                                        <div class="form-text">
                                                            Los doctores inactivos no aparecerán disponibles para nuevas citas
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                                <i class="fas fa-arrow-left me-1"></i>
                                                Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                                Siguiente: Contacto
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Contact Information -->
                                <div class="col-12 form-step-content d-none" data-step="3">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-phone me-2"></i>Información de Contacto</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone" class="form-label required">Teléfono</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-phone"></i>
                                                        </span>
                                                        <input type="tel" 
                                                            class="form-control @error('phone') is-invalid @enderror" 
                                                            id="phone" 
                                                            name="phone" 
                                                            value="{{ old('phone', $doctor->phone ?? '') }}" 
                                                            required
                                                            placeholder="Ej: 555-123-4567"
                                                            autocomplete="tel">
                                                    </div>
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email" class="form-label">Correo Electrónico</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-envelope"></i>
                                                        </span>
                                                        <input type="email" 
                                                            class="form-control @error('email') is-invalid @enderror" 
                                                            id="email" 
                                                            name="email" 
                                                            value="{{ old('email', $doctor->email ?? '') }}"
                                                            placeholder="doctor@ejemplo.com"
                                                            autocomplete="email">
                                                    </div>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        El email es opcional pero recomendado para notificaciones
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                                <i class="fas fa-arrow-left me-1"></i>
                                                Anterior
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-save me-1"></i>
                                                {{ isset($doctor) ? 'Actualizar Doctor' : 'Crear Doctor' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Summary (Always Visible) -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-summary">
                                        <div class="form-actions">
                                            <a href="{{ route('doctors.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Cancelar
                                            </a>
                                            <button type="button" class="btn btn-info" onclick="resetForm()">
                                                <i class="fas fa-undo me-1"></i>
                                                Limpiar Formulario
                                            </button>
                                            @if(isset($doctor))
                                                <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    Programar Cita
                                                </a>
                                            @endif
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
    const form = document.getElementById('doctorForm');
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const licenseInput = document.getElementById('license_number');
    const specialtyInput = document.getElementById('specialty');

    // Real-time validation for names (only letters and spaces)
    [nameInput, lastNameInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                validateField(this);
            });
        }
    });

    // Phone formatting and validation
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1-$2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d+)/, '$1-$2');
            }
            this.value = value;
            validateField(this);
        });
    }

    // Email validation
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateField(this);
        });
    }

    // License number formatting (only alphanumeric)
    if (licenseInput) {
        licenseInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            validateField(this);
        });
    }

    // Specialty validation
    if (specialtyInput) {
        specialtyInput.addEventListener('input', function() {
            validateField(this);
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        
        // Remove previous validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Skip validation for optional fields
        if (!field.hasAttribute('required') && !value) {
            return true;
        }

        let isValid = true;
        
        switch (field.type) {
            case 'email':
                isValid = value === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                break;
            case 'tel':
                isValid = value === '' || value.length >= 10;
                break;
            default:
                isValid = field.hasAttribute('required') ? value !== '' : true;
        }

        // Additional validation for license number (must be unique when editing)
        if (field.name === 'license_number' && value && isValid) {
            // This would typically be validated server-side
            // For now, just check basic format
            isValid = value.length >= 4;
        }

        // Add validation class
        field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        return isValid;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        // Validate all fields
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
            // Go to first step with errors
            const firstStepWithError = document.querySelector('.form-step-content .is-invalid')?.closest('.form-step-content');
            if (firstStepWithError) {
                const stepNumber = firstStepWithError.dataset.step;
                goToStep(parseInt(stepNumber));
            }
            return;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
            
            // Re-enable after 10 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        }
    });
});

// Step navigation functions
function goToStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.form-step-content').forEach(step => {
        step.classList.add('d-none');
    });
    
    // Show target step
    const targetStep = document.querySelector(`[data-step="${stepNumber}"]`);
    if (targetStep) {
        targetStep.classList.remove('d-none');
    }
    
    // Update step indicators
    document.querySelectorAll('.form-step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 < stepNumber) {
            step.classList.add('completed');
        } else if (index + 1 === stepNumber) {
            step.classList.add('active');
        }
    });
}

function nextStep(stepNumber) {
    // Validate current step before proceeding
    const currentStep = document.querySelector('.form-step-content:not(.d-none)');
    const requiredFields = currentStep.querySelectorAll('[required]');
    let isStepValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isStepValid = false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });
    
    if (!isStepValid) {
        showToast('Por favor complete todos los campos requeridos en este paso', 'warning');
        return;
    }
    
    goToStep(stepNumber);
}

function prevStep(stepNumber) {
    goToStep(stepNumber);
}

function resetForm() {
    if (confirm('¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.')) {
        document.getElementById('doctorForm').reset();
        document.querySelectorAll('.form-control, .form-select').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        goToStep(1);
        showToast('Formulario limpiado', 'info');
    }
}

// Toast notification function
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