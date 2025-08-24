@extends('layouts.app')

@section('title', isset($patient) ? 'Editar Paciente' : 'Nuevo Paciente')

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
            <h2 class="page-title">{{ isset($patient) ? 'Editar Paciente' : 'Nuevo Paciente' }}</h2>
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
                            <a href="{{ route('patients.index') }}">Pacientes</a>
                        </li>
                        @if(isset($patient))
                            <li class="breadcrumb-item">
                                <a href="{{ route('patients.show', $patient) }}">{{ $patient->getFullNameAttribute() }}</a>
                            </li>
                            <li class="breadcrumb-item active">Editar</li>
                        @else
                            <li class="breadcrumb-item active">Nuevo Paciente</li>
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
                        <span>Contacto</span>
                    </div>
                    <div class="form-step">
                        <div class="form-step-number">3</div>
                        <span>Información Médica</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Form -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-{{ isset($patient) ? 'user-edit' : 'user-plus' }} widget-icon"></i>
                            <h5>{{ isset($patient) ? 'Editar Información del Paciente' : 'Información del Nuevo Paciente' }}</h5>
                            <span class="widget-subtitle">
                                {{ isset($patient) ? 'Actualice los datos del paciente' : 'Complete todos los campos requeridos' }}
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                            @if(isset($patient))
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalles
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <form method="POST" 
                              action="{{ isset($patient) ? route('patients.update', $patient) : route('patients.store') }}" 
                              data-loading 
                              id="patientForm">
                            @csrf
                            @if(isset($patient))
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
                                                               value="{{ old('name', $patient->name ?? '') }}" 
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
                                                               value="{{ old('last_name', $patient->last_name ?? '') }}" 
                                                               required
                                                               placeholder="Ingrese los apellidos"
                                                               autocomplete="family-name">
                                                    </div>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-calendar"></i>
                                                        </span>
                                                        <input type="date" 
                                                               class="form-control @error('birth_date') is-invalid @enderror" 
                                                               id="birth_date" 
                                                               name="birth_date" 
                                                               value="{{ old('birth_date', isset($patient) && $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '') }}"
                                                               max="{{ date('Y-m-d') }}"
                                                               autocomplete="bday">
                                                    </div>
                                                    <div id="age-display" class="form-text"></div>
                                                    @error('birth_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gender" class="form-label">Género</label>
                                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                                            id="gender" 
                                                            name="gender">
                                                        <option value="">Seleccionar género</option>
                                                        <option value="masculino" {{ old('gender', $patient->gender ?? '') == 'masculino' ? 'selected' : '' }}>
                                                            Masculino
                                                        </option>
                                                        <option value="femenino" {{ old('gender', $patient->gender ?? '') == 'femenino' ? 'selected' : '' }}>
                                                            Femenino
                                                        </option>
                                                        <option value="otro" {{ old('gender', $patient->gender ?? '') == 'otro' ? 'selected' : '' }}>
                                                            Otro
                                                        </option>
                                                    </select>
                                                    @error('gender')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="rfc_nit" class="form-label">RFC/NIT</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-id-card"></i>
                                                        </span>
                                                        <input type="text" 
                                                               class="form-control @error('rfc_nit') is-invalid @enderror" 
                                                               id="rfc_nit" 
                                                               name="rfc_nit" 
                                                               value="{{ old('rfc_nit', $patient->rfc_nit ?? '') }}"
                                                               placeholder="RFC o NIT del paciente (opcional)"
                                                               style="text-transform: uppercase;">
                                                    </div>
                                                    @error('rfc_nit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                                Siguiente: Contacto
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Contact Information -->
                                <div class="col-12 form-step-content d-none" data-step="2">
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
                                                               value="{{ old('phone', $patient->phone ?? '') }}" 
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
                                                               value="{{ old('email', $patient->email ?? '') }}"
                                                               placeholder="ejemplo@correo.com"
                                                               autocomplete="email">
                                                    </div>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address" class="form-label">Dirección Completa</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </span>
                                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                                  id="address" 
                                                                  name="address" 
                                                                  rows="3"
                                                                  placeholder="Calle, número, colonia, ciudad, código postal..."
                                                                  autocomplete="street-address">{{ old('address', $patient->address ?? '') }}</textarea>
                                                    </div>
                                                    @error('address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                                <i class="fas fa-arrow-left me-1"></i>
                                                Anterior
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                                Siguiente: Información Médica
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Medical Information -->
                                <div class="col-12 form-step-content d-none" data-step="3">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-file-medical me-2"></i>Información Médica</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="medical_notes" class="form-label">Notas Médicas</label>
                                                    <textarea class="form-control @error('medical_notes') is-invalid @enderror" 
                                                              id="medical_notes" 
                                                              name="medical_notes" 
                                                              rows="6"
                                                              placeholder="Información médica relevante: alergias, condiciones preexistentes, medicamentos actuales, cirugías previas, etc.">{{ old('medical_notes', $patient->medical_notes ?? '') }}</textarea>
                                                    @error('medical_notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Incluya información sobre alergias conocidas, condiciones médicas preexistentes, 
                                                        medicamentos que toma actualmente, cirugías previas, y cualquier otra información 
                                                        médica relevante que pueda ser importante para el tratamiento.
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(isset($patient))
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Estado del Paciente</label>
                                                    <div class="form-switch">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               role="switch" 
                                                               id="is_active" 
                                                               name="is_active" 
                                                               value="1" 
                                                               {{ old('is_active', $patient->is_active) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">
                                                            Paciente Activo
                                                        </label>
                                                    </div>
                                                    <div class="form-text">
                                                        Los pacientes inactivos no aparecerán en las búsquedas principales
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="form-step-actions mt-4">
                                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                                <i class="fas fa-arrow-left me-1"></i>
                                                Anterior
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-save me-1"></i>
                                                {{ isset($patient) ? 'Actualizar Paciente' : 'Crear Paciente' }}
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
                                            <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Cancelar
                                            </a>
                                            <button type="button" class="btn btn-info" onclick="resetForm()">
                                                <i class="fas fa-undo me-1"></i>
                                                Limpiar Formulario
                                            </button>
                                            @if(isset($patient))
                                                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" 
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
   
    // Form validation and interactions
    const form = document.getElementById('patientForm');
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const birthDateInput = document.getElementById('birth_date');
    const rfcInput = document.getElementById('rfc_nit');

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

    // RFC/NIT formatting
    if (rfcInput) {
        rfcInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            validateField(this);
        });
    }

    // Birth date and age calculation
    if (birthDateInput) {
        birthDateInput.addEventListener('change', function() {
            calculateAge();
            validateField(this);
        });
        
        // Calculate age on page load if date exists
        if (birthDateInput.value) {
            calculateAge();
        }
    }

    function calculateAge() {
        const birthDate = new Date(birthDateInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        const ageDisplay = document.getElementById('age-display');
        if (birthDateInput.value && age >= 0) {
            ageDisplay.textContent = `Edad: ${age} años`;
            ageDisplay.classList.add('text-primary');
        } else {
            ageDisplay.textContent = '';
        }
    }

    function validateField(field) {
        const value = field.value.trim();
        
        // Remove previous validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Skip validation for optional fields
        if (!field.hasAttribute('required') && !value) {
            return;
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

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
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
        document.getElementById('patientForm').reset();
        document.querySelectorAll('.form-control, .form-select').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        document.getElementById('age-display').textContent = '';
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
    
    // Remove toast after delay
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

<style>
/* Form-specific styles */
.form-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
}

.form-step {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: #e5e7eb;
    color: #6b7280;
    border-radius: 25px;
    transition: all 0.3s ease;
    position: relative;
}

.form-step::after {
    content: '';
    position: absolute;
    right: -1.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 12px solid #e5e7eb;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    transition: border-left-color 0.3s ease;
}

.form-step:last-child::after {
    display: none;
}

.form-step.active {
    background: #3b82f6;
    color: white;
    transform: scale(1.05);
}

.form-step.active::after {
    border-left-color: #3b82f6;
}

.form-step.completed {
    background: #10b981;
    color: white;
}

.form-step.completed::after {
    border-left-color: #10b981;
}

.form-step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    margin-right: 0.75rem;
    font-size: 0.875rem;
    font-weight: 700;
}

.form-step.completed .form-step-number::before {
    content: '✓';
    font-weight: bold;
}

.form-step.completed .form-step-number {
    background: rgba(255, 255, 255, 0.3);
}

.form-step-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.form-summary {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
}

.form-switch {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.form-switch .form-check-input {
    width: 48px;
    height: 24px;
    border-radius: 12px;
    background-color: #d1d5db;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-switch .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.form-switch .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-switch .form-check-label {
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    margin-bottom: 0;
}

/* Toast styles */
.toast-container {
    z-index: 9999;
}

.toast {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-left: 4px solid #3b82f6;
    min-width: 300px;
    margin-bottom: 0.5rem;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success {
    border-left-color: #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
}

.toast.error {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
}

.toast.warning {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
}

.toast.info {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
}

/* Input group improvements */
.input-group .input-group-text {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 2px solid #e5e7eb;
    color: #6b7280;
    font-weight: 500;
}

.input-group .form-control:focus + .input-group-text,
.input-group .input-group-text + .form-control:focus {
    border-color: #3b82f6;
}

.input-group .form-control:focus + .input-group-text {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: #3b82f6;
}

/* Validation styles */
.is-valid {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

/* Age display */
#age-display {
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-steps {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-step {
        width: 100%;
        justify-content: center;
    }
    
    .form-step::after {
        display: none;
    }
    
    .form-step-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-step-actions .btn {
        width: 100%;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .widget-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .widget-actions .btn {
        width: 100%;
        font-size: 0.875rem;
    }
    
    .toast {
        min-width: auto;
        width: 90vw;
    }
}
</style>

@endsection