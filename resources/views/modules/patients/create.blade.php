@extends('layouts.app')

@section('title', 'Nuevo Paciente')

@section('content')
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand-section">
            <div class="brand-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h4 class="brand-text">ClinicaPro</h4>
        </div>
        
        <!-- User Profile Card -->
        <div class="user-profile-card">
            <div class="user-avatar">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="user-info">
                <h6 class="user-name">{{ auth()->user()->name }}</h6>
                <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
            <div class="status-indicator"></div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-pie nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('patients.index') }}">
                    <i class="fas fa-users nav-icon"></i>
                    <span>Pacientes</span>
                    <div class="nav-indicator"></div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#appointments">
                    <i class="fas fa-calendar-alt nav-icon"></i>
                    <span>Agenda</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#invoices">
                    <i class="fas fa-file-invoice-dollar nav-icon"></i>
                    <span>Facturación</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#inventory">
                    <i class="fas fa-boxes nav-icon"></i>
                    <span>Inventario</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#expenses">
                    <i class="fas fa-receipt nav-icon"></i>
                    <span>Gastos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#reports">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span>Reportes</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout Button -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Nuevo Paciente</h2>
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
                        <li class="breadcrumb-item active">Nuevo Paciente</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Patient Form -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-user-plus widget-icon"></i>
                            <h5>Información del Paciente</h5>
                            <span class="widget-subtitle">Complete todos los campos requeridos</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <form method="POST" action="{{ route('patients.store') }}" data-loading>
                            @csrf
                            
                            <div class="row g-4">
                                <!-- Personal Information Section -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-user me-2"></i>Información Personal</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label required">Nombre</label>
                                                    <input type="text" 
                                                           class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" 
                                                           name="name" 
                                                           value="{{ old('name') }}" 
                                                           required
                                                           placeholder="Ingrese el nombre">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name" class="form-label required">Apellidos</label>
                                                    <input type="text" 
                                                           class="form-control @error('last_name') is-invalid @enderror" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="{{ old('last_name') }}" 
                                                           required
                                                           placeholder="Ingrese los apellidos">
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                                    <input type="date" 
                                                           class="form-control @error('birth_date') is-invalid @enderror" 
                                                           id="birth_date" 
                                                           name="birth_date" 
                                                           value="{{ old('birth_date') }}"
                                                           max="{{ date('Y-m-d') }}">
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
                                                        <option value="masculino" {{ old('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                                        <option value="femenino" {{ old('gender') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                                        <option value="otro" {{ old('gender') == 'otro' ? 'selected' : '' }}>Otro</option>
                                                    </select>
                                                    @error('gender')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Section -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-phone me-2"></i>Información de Contacto</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone" class="form-label required">Teléfono</label>
                                                    <input type="tel" 
                                                           class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" 
                                                           name="phone" 
                                                           value="{{ old('phone') }}" 
                                                           required
                                                           placeholder="Ej: 555-123-4567">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" 
                                                           class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" 
                                                           name="email" 
                                                           value="{{ old('email') }}"
                                                           placeholder="ejemplo@correo.com">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address" class="form-label">Dirección</label>
                                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                                              id="address" 
                                                              name="address" 
                                                              rows="3"
                                                              placeholder="Ingrese la dirección completa">{{ old('address') }}</textarea>
                                                    @error('address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information Section -->
                                <div class="col-12">
                                    <div class="form-section">
                                        <div class="form-section-header">
                                            <h6><i class="fas fa-file-medical me-2"></i>Información Adicional</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rfc_nit" class="form-label">RFC/NIT</label>
                                                    <input type="text" 
                                                           class="form-control @error('rfc_nit') is-invalid @enderror" 
                                                           id="rfc_nit" 
                                                           name="rfc_nit" 
                                                           value="{{ old('rfc_nit') }}"
                                                           placeholder="RFC o NIT del paciente">
                                                    @error('rfc_nit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="medical_notes" class="form-label">Notas Médicas</label>
                                                    <textarea class="form-control @error('medical_notes') is-invalid @enderror" 
                                                              id="medical_notes" 
                                                              name="medical_notes" 
                                                              rows="4"
                                                              placeholder="Alergias, condiciones médicas, medicamentos actuales, etc.">{{ old('medical_notes') }}</textarea>
                                                    @error('medical_notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        Incluya información relevante sobre alergias, condiciones médicas preexistentes, medicamentos actuales, etc.
                                                    </div>
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
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Guardar Paciente
                                        </button>
                                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Cancelar
                                        </a>
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
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebarToggle.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    // Form validation
    const form = document.querySelector('form[data-loading]');
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');

    // Real-time validation
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    if (lastNameInput) {
        lastNameInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    // Phone formatting
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1-$2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d+)/, '$1-$2');
            }
            this.value = value;
        });
    }

    // Email validation
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value && !this.value.includes('@')) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Ingrese un email válido';
                    this.parentNode.appendChild(feedback);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            // Clear any previous validation states
            const invalidElements = form.querySelectorAll('.is-invalid');
            invalidElements.forEach(element => {
                element.classList.remove('is-invalid');
            });

            // Basic validation
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                showGlobalAlert('Por favor complete todos los campos requeridos', 'error');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
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
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
        }
    });
});
</script>

@endsection