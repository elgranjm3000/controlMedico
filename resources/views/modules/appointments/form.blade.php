@extends('layouts.app')

@section('title', isset($appointment) ? 'Editar Cita' : 'Nueva Cita')

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
            <h2 class="page-title">{{ isset($appointment) ? 'Editar Cita' : 'Nueva Cita' }}</h2>
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
                            <a href="{{ route('appointments.index') }}">Citas</a>
                        </li>
                        @if(isset($appointment))
                            <li class="breadcrumb-item">
                                <a href="{{ route('appointments.show', $appointment) }}">{{ $appointment->patient->getFullNameAttribute() }}</a>
                            </li>
                            <li class="breadcrumb-item active">Editar</li>
                        @else
                            <li class="breadcrumb-item active">Nueva Cita</li>
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
                        <span>Paciente y Doctor</span>
                    </div>
                    <div class="form-step">
                        <div class="form-step-number">2</div>
                        <span>Fecha y Hora</span>
                    </div>
                    <div class="form-step">
                        <div class="form-step-number">3</div>
                        <span>Detalles</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Form -->
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-{{ isset($appointment) ? 'calendar-check' : 'calendar-plus' }} widget-icon"></i>
                            <h5>{{ isset($appointment) ? 'Editar Información de la Cita' : 'Información de la Nueva Cita' }}</h5>
                            <span class="widget-subtitle">
                                {{ isset($appointment) ? 'Actualice los datos de la cita' : 'Complete todos los campos requeridos' }}
                            </span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                            @if(isset($appointment))
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver Detalles
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="widget-body">
                        <form method="POST" 
                              action="{{ isset($appointment) ? route('appointments.update', $appointment) : route('appointments.store') }}" 
                              data-loading 
                              id="appointmentForm">
                            @csrf
                            @if(isset($appointment))
                                @method('PUT')
                            @endif

                            <!-- Step 1: Patient and Doctor -->
                            <div class="form-step-content" data-step="1">
                                <div class="form-section">
                                    <div class="form-section-header">
                                        <h6><i class="fas fa-users me-2"></i>Paciente y Doctor</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="patient_id" class="form-label required">Paciente</label>
                                                <select class="form-select @error('patient_id') is-invalid @enderror" 
                                                        id="patient_id" 
                                                        name="patient_id" 
                                                        required>
                                                    <option value="">Seleccionar paciente</option>
                                                    @foreach($patients as $patient)
                                                        <option value="{{ $patient->id }}" 
                                                            {{ (string) old('patient_id', $selectedPatient->id ?? $appointment->patient_id ?? '') == (string) $patient->id ? 'selected' : '' }}>
                                                            {{ $patient->getFullNameAttribute() }}
                                                            @if($patient->phone)
                                                                - {{ $patient->phone }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('patient_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Si no encuentra el paciente, puede 
                                                    <a href="{{ route('patients.create') }}" target="_blank">crear uno nuevo</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="doctor_id" class="form-label required">Doctor</label>
                                                <select class="form-select @error('doctor_id') is-invalid @enderror" 
                                                        id="doctor_id" 
                                                        name="doctor_id" 
                                                        required>
                                                    <option value="">Seleccionar doctor</option>
                                                    @foreach($doctors as $doctor)
                                                        <option value="{{ $doctor->id }}" 
                                                            {{ old('doctor_id', $appointment->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                                            {{ $doctor->getFullNameAttribute() }}
                                                            - {{ $doctor->specialty }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('doctor_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="consultation_room_id" class="form-label required">Sala de Consulta</label>
                                                <select class="form-select @error('consultation_room_id') is-invalid @enderror" 
                                                        id="consultation_room_id" 
                                                        name="consultation_room_id" 
                                                        required>
                                                    <option value="">Seleccionar sala</option>
                                                    @foreach($consultationRooms as $room)
                                                        <option value="{{ $room->id }}" 
                                                            {{ old('consultation_room_id', $appointment->consultation_room_id ?? '') == $room->id ? 'selected' : '' }}>
                                                            {{ $room->name }}
                                                            @if($room->location)
                                                                - {{ $room->location }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('consultation_room_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duration_minutes" class="form-label required">Duración (minutos)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                    <select class="form-select @error('duration_minutes') is-invalid @enderror" 
                                                            id="duration_minutes" 
                                                            name="duration_minutes" 
                                                            required>
                                                        <option value="15" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 15 ? 'selected' : '' }}>15 minutos</option>
                                                        <option value="30" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 30 ? 'selected' : '' }}>30 minutos</option>
                                                        <option value="45" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 45 ? 'selected' : '' }}>45 minutos</option>
                                                        <option value="60" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 60 ? 'selected' : '' }}>1 hora</option>
                                                        <option value="90" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 90 ? 'selected' : '' }}>1.5 horas</option>
                                                        <option value="120" {{ old('duration_minutes', $appointment->duration_minutes ?? 30) == 120 ? 'selected' : '' }}>2 horas</option>
                                                    </select>
                                                </div>
                                                @error('duration_minutes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-step-actions mt-4">
                                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                            Siguiente: Fecha y Hora
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Date and Time -->
                            <div class="form-step-content d-none" data-step="2">
                                <div class="form-section">
                                    <div class="form-section-header">
                                        <h6><i class="fas fa-calendar-alt me-2"></i>Fecha y Hora</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="appointment_date" class="form-label required">Fecha de la Cita</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                    <input type="date" 
                                                           class="form-control @error('scheduled_at') is-invalid @enderror" 
                                                           id="appointment_date" 
                                                           name="appointment_date" 
                                                           value="{{ old('appointment_date', isset($appointment) ? $appointment->scheduled_at->format('Y-m-d') : '') }}" 
                                                           required
                                                           min="{{ date('Y-m-d') }}"
                                                           onchange="updateAvailableSlots()">
                                                </div>
                                                @error('scheduled_at')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="appointment_time" class="form-label required">Hora de la Cita</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                    <select class="form-select @error('scheduled_at') is-invalid @enderror" 
                                                            id="appointment_time" 
                                                            name="appointment_time" 
                                                            required>
                                                        <option value="">Primero seleccione una fecha</option>
                                                    </select>
                                                    <input type="hidden" 
                                                           name="scheduled_at" 
                                                           id="scheduled_at" 
                                                           value="{{ old('scheduled_at', isset($appointment) ? $appointment->scheduled_at->format('Y-m-d H:i:s') : '') }}">
                                                </div>
                                                <div id="time-loading" class="form-text d-none">
                                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                                    Cargando horarios disponibles...
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Información:</strong> Los horarios mostrados están disponibles según la disponibilidad del doctor y la sala de consulta seleccionados.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-step-actions mt-4">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Anterior
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                            Siguiente: Detalles
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Details -->
                            <div class="form-step-content d-none" data-step="3">
                                <div class="form-section">
                                    <div class="form-section-header">
                                        <h6><i class="fas fa-file-medical me-2"></i>Detalles de la Cita</h6>
                                    </div>
                                    <div class="row g-3">
                                        @if(isset($appointment))
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status" class="form-label">Estado de la Cita</label>
                                                    <select class="form-select @error('status') is-invalid @enderror" 
                                                            id="status" 
                                                            name="status">
                                                        <option value="programada" {{ old('status', $appointment->status) == 'programada' ? 'selected' : '' }}>
                                                            Programada
                                                        </option>
                                                        <option value="confirmada" {{ old('status', $appointment->status) == 'confirmada' ? 'selected' : '' }}>
                                                            Confirmada
                                                        </option>
                                                        <option value="en_curso" {{ old('status', $appointment->status) == 'en_curso' ? 'selected' : '' }}>
                                                            En Curso
                                                        </option>
                                                        <option value="completada" {{ old('status', $appointment->status) == 'completada' ? 'selected' : '' }}>
                                                            Completada
                                                        </option>
                                                        <option value="cancelada" {{ old('status', $appointment->status) == 'cancelada' ? 'selected' : '' }}>
                                                            Cancelada
                                                        </option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="notes" class="form-label">Notas y Observaciones</label>
                                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                          id="notes" 
                                                          name="notes" 
                                                          rows="4"
                                                          placeholder="Motivo de la consulta, síntomas, observaciones especiales...">{{ old('notes', $appointment->notes ?? '') }}</textarea>
                                                @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Incluya información relevante sobre el motivo de la consulta o instrucciones especiales.
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
                                            {{ isset($appointment) ? 'Actualizar Cita' : 'Crear Cita' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Summary (Always Visible) -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-summary">
                                        <div class="form-actions">
                                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Cancelar
                                            </a>
                                            <button type="button" class="btn btn-info" onclick="resetForm()">
                                                <i class="fas fa-undo me-1"></i>
                                                Limpiar Formulario
                                            </button>
                                            @if(isset($appointment) && !$appointment->invoice)
                                                <a href="{{ route('invoices.create', ['appointment_id' => $appointment->id]) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-file-invoice me-1"></i>
                                                    Crear Factura
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
    const form = document.getElementById('appointmentForm');
    const patientSelect = document.getElementById('patient_id');
    const doctorSelect = document.getElementById('doctor_id');
    const roomSelect = document.getElementById('consultation_room_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const durationSelect = document.getElementById('duration_minutes');
    const scheduledAtInput = document.getElementById('scheduled_at');

    // Update scheduled_at when date and time change
    function updateScheduledAt() {
        const date = dateInput.value;
        const time = timeSelect.value;
        if (date && time) {
            scheduledAtInput.value = `${date} ${time}:00`;
        }
    }

    dateInput.addEventListener('change', updateScheduledAt);
    timeSelect.addEventListener('change', updateScheduledAt);

    // Form validation
    function validateStep(stepNumber) {
        let isValid = true;
        const currentStep = document.querySelector(`[data-step="${stepNumber}"]`);
        const requiredFields = currentStep.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        return isValid;
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        let isFormValid = true;
        
        // Validate all steps
        for (let i = 1; i <= 3; i++) {
            if (!validateStep(i)) {
                isFormValid = false;
                goToStep(i);
                break;
            }
        }

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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
            
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        }
    });

    // Initialize form if editing
    @if(isset($appointment))
        updateAvailableSlots();
    @endif
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
    const currentStep = stepNumber - 1;
    
    // Validate current step
    if (!validateCurrentStep(currentStep)) {
        showToast('Por favor complete todos los campos requeridos en este paso', 'warning');
        return;
    }
    
    // Special validation for step 2 (check available slots)
    if (currentStep === 1 && stepNumber === 2) {
        updateAvailableSlots();
    }
    
    goToStep(stepNumber);
}

function prevStep(stepNumber) {
    goToStep(stepNumber);
}

function validateCurrentStep(stepNumber) {
    const currentStep = document.querySelector(`[data-step="${stepNumber}"]`);
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
    
    return isStepValid;
}

function updateAvailableSlots() {
    const doctorId = document.getElementById('doctor_id').value;
    const roomId = document.getElementById('consultation_room_id').value;
    const date = document.getElementById('appointment_date').value;
    const duration = document.getElementById('duration_minutes').value;
    const timeSelect = document.getElementById('appointment_time');
    const loadingDiv = document.getElementById('time-loading');
    
    if (!doctorId || !roomId || !date || !duration) {
        timeSelect.innerHTML = '<option value="">Complete los campos anteriores primero</option>';
        return;
    }
    
    loadingDiv.classList.remove('d-none');
    timeSelect.disabled = true;
    
    axios.get('/api/appointments/available-slots', {
        params: {
            doctor_id: doctorId,
            room_id: roomId,
            date: date,
            duration: duration
        }
    })
    .then(response => {
        const slots = response.data.slots;
        timeSelect.innerHTML = '';
        
        if (slots.length === 0) {
            timeSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
        } else {
            timeSelect.innerHTML = '<option value="">Seleccionar hora</option>';
            slots.forEach(slot => {
                timeSelect.innerHTML += `<option value="${slot.time}">${slot.display}</option>`;
            });
            
            // If editing, try to select current time
            @if(isset($appointment))
                const currentTime = '{{ $appointment->scheduled_at->format("H:i") }}';
                const currentOption = timeSelect.querySelector(`option[value="${currentTime}"]`);
                if (currentOption) {
                    timeSelect.value = currentTime;
                }
            @endif
        }
        
        loadingDiv.classList.add('d-none');
        timeSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error loading slots:', error);
        timeSelect.innerHTML = '<option value="">Error cargando horarios</option>';
        loadingDiv.classList.add('d-none');
        timeSelect.disabled = false;
        showToast('Error al cargar los horarios disponibles', 'error');
    });
}

function resetForm() {
    if (confirm('¿Está seguro de que desea limpiar el formulario? Se perderán todos los datos ingresados.')) {
        document.getElementById('appointmentForm').reset();
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