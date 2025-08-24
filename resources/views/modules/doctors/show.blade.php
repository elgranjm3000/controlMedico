@extends('layouts.app')

@section('title', 'Doctor: ' . $doctor->getFullNameAttribute())

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
            <h2 class="page-title">Perfil del Doctor</h2>
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
                            <a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('doctors.index') }}"><i class="fas fa-user-md me-1"></i>Doctores</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-user me-1"></i>{{ $doctor->getFullNameAttribute() }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Doctor Header Card -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="patient-header-card">
                    <div class="patient-hero">
                        <div class="patient-hero-bg"></div>
                        <div class="patient-hero-content">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="patient-main-info">
                                        <div class="patient-avatar-large">
                                            <i class="fas fa-user-md"></i>
                                            <div class="status-indicator {{ $doctor->is_active ? 'online' : 'offline' }}"></div>
                                        </div>
                                        <div class="patient-details-large">
                                            <h1 class="patient-name-large">
                                                {{ $doctor->getFullNameAttribute() }}
                                                @if(!$doctor->is_active)
                                                    <span class="badge bg-warning text-dark ms-2">
                                                        <i class="fas fa-pause"></i> Inactivo
                                                    </span>
                                                @endif
                                            </h1>
                                            <div class="patient-meta-large">
                                                <span class="meta-item">
                                                    <i class="fas fa-stethoscope"></i>
                                                    {{ $doctor->specialty }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-id-card"></i>
                                                    Cédula: {{ $doctor->license_number }}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-calendar-plus"></i>
                                                    Registrado {{ $doctor->created_at->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="patient-actions">
                                        <div class="action-group">
                                            <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-outline-light btn-action">
                                                <i class="fas fa-edit"></i>
                                                <span>Editar</span>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle btn-action" type="button" 
                                                    id="doctorActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorActionsDropdown">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}">
                                                            <i class="fas fa-calendar-plus me-2"></i>Nueva Cita
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="viewSchedule()">
                                                            <i class="fas fa-calendar me-2"></i>Ver Agenda
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="printDoctorInfo()">
                                                            <i class="fas fa-print me-2"></i>Imprimir Info
                                                        </button>
                                                    </li>
                                                    @if($doctor->is_active)
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-warning" onclick="deactivateDoctor('{{ $doctor->id }}')">
                                                                <i class="fas fa-pause me-2"></i>Desactivar
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-appointments">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number">{{ $totalAppointments }}</div>
                    <div class="stat-label">Citas Totales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-completed">
                    <div class="stat-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $completedAppointments }}</div>
                    <div class="stat-label">Citas Completadas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-invoices">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-number">{{ $todayAppointments->count() }}</div>
                    <div class="stat-label">Citas de Hoy</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-revenue">
                    <div class="stat-icon text-info">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number">
                        @if($totalAppointments > 0)
                            {{ number_format(($completedAppointments / $totalAppointments) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                    <div class="stat-label">Tasa de Completación</div>
                </div>
            </div>
        </div>

        <!-- Doctor Details Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs patient-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-info-tab" data-bs-toggle="tab" 
                                    data-bs-target="#nav-info" type="button" role="tab">
                                    <i class="fas fa-info-circle me-2"></i>Información General
                                </button>
                                <button class="nav-link" id="nav-appointments-tab" data-bs-toggle="tab" 
                                    data-bs-target="#nav-appointments" type="button" role="tab">
                                    <i class="fas fa-calendar-alt me-2"></i>Citas de Hoy
                                </button>
                                <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" 
                                    data-bs-target="#nav-history" type="button" role="tab">
                                    <i class="fas fa-history me-2"></i>Historial
                                </button>
                            </div>
                        </nav>
                        <div class="tab-content mt-4" id="nav-tabContent">
                            <!-- General Information Tab -->
                            <div class="tab-pane fade show active" id="nav-info" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <h5><i class="fas fa-user-md me-2"></i>Información Profesional</h5>
                                            </div>
                                            <div class="info-card-body">
                                                <div class="info-list">
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-user"></i>
                                                            Nombre Completo:
                                                        </span>
                                                        <span class="info-value">{{ $doctor->getFullNameAttribute() }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-stethoscope"></i>
                                                            Especialidad:
                                                        </span>
                                                        <span class="info-value">{{ $doctor->specialty }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-id-card"></i>
                                                            Número de Cédula:
                                                        </span>
                                                        <span class="info-value">{{ $doctor->license_number }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-calendar-plus"></i>
                                                            Fecha de Registro:
                                                        </span>
                                                        <span class="info-value">
                                                            {{ $doctor->created_at->format('d/m/Y H:i') }}
                                                            <small class="text-muted">({{ $doctor->created_at->diffForHumans() }})</small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-card-header">
                                                <h5><i class="fas fa-phone me-2"></i>Información de Contacto</h5>
                                            </div>
                                            <div class="info-card-body">
                                                <div class="info-list">
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-phone"></i>
                                                            Teléfono:
                                                        </span>
                                                        <span class="info-value">
                                                            <a href="tel:{{ $doctor->phone }}" class="contact-link">
                                                                {{ $doctor->phone }}
                                                                <i class="fas fa-external-link-alt ms-1"></i>
                                                            </a>
                                                        </span>
                                                    </div>
                                                    @if($doctor->email)
                                                        <div class="info-item">
                                                            <span class="info-label">
                                                                <i class="fas fa-envelope"></i>
                                                                Email:
                                                            </span>
                                                            <span class="info-value">
                                                                <a href="mailto:{{ $doctor->email }}" class="contact-link">
                                                                    {{ $doctor->email }}
                                                                    <i class="fas fa-external-link-alt ms-1"></i>
                                                                </a>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-toggle-{{ $doctor->is_active ? 'on' : 'off' }}"></i>
                                                            Estado:
                                                        </span>
                                                        <span class="info-value">
                                                            @if($doctor->is_active)
                                                                <span class="status-badge status-active">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    Activo
                                                                </span>
                                                            @else
                                                                <span class="status-badge status-inactive">
                                                                    <i class="fas fa-pause-circle"></i>
                                                                    Inactivo
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Today's Appointments Tab -->
                            <div class="tab-pane fade" id="nav-appointments" role="tabpanel">
                                @if($todayAppointments->count() > 0)
                                    <div class="appointments-timeline">
                                        @foreach($todayAppointments as $appointment)
                                            <div class="timeline-item appointment-timeline-item status-{{ $appointment->status }}">
                                                <div class="timeline-marker">
                                                    <i class="fas fa-{{ $appointment->status === 'completada' ? 'check' : ($appointment->status === 'cancelada' ? 'times' : 'clock') }}"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="timeline-header">
                                                        <div class="timeline-date">
                                                            <strong>{{ $appointment->scheduled_at->format('H:i') }}</strong>
                                                            <span class="timeline-time">{{ $appointment->duration_minutes }} min</span>
                                                        </div>
                                                        <span class="status-badge status-{{ $appointment->status }}">
                                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                                        </span>
                                                    </div>
                                                    <div class="timeline-body">
                                                        <div class="appointment-details">
                                                            <p class="mb-2">
                                                                <i class="fas fa-user me-2"></i>
                                                                <strong>{{ $appointment->patient->getFullNameAttribute() }}</strong>
                                                                <a href="{{ route('patients.show', $appointment->patient) }}" class="ms-1">
                                                                    <i class="fas fa-external-link-alt"></i>
                                                                </a>
                                                            </p>
                                                            <p class="mb-2">
                                                                <i class="fas fa-door-open me-2"></i>
                                                                {{ $appointment->consultationRoom->name }}
                                                            </p>
                                                            <p class="mb-0">
                                                                <i class="fas fa-phone me-2"></i>
                                                                {{ $appointment->patient->phone }}
                                                            </p>
                                                            @if($appointment->notes)
                                                                <div class="appointment-notes mt-2">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-sticky-note me-1"></i>
                                                                        {{ $appointment->notes }}
                                                                    </small>
                                                                </div>
                                                            @endif
                                                            <div class="mt-2">
                                                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-outline-primary">
                                                                    Ver Detalles
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <h5>No hay citas para hoy</h5>
                                        <p>Este doctor no tiene citas programadas para hoy.</p>
                                        <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            Programar Nueva Cita
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="nav-history" role="tabpanel">
                                @if($doctor->appointments()->count() > 0)
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Paciente</th>
                                                            <th>Estado</th>
                                                            <th>Duración</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($doctor->appointments()->with('patient')->orderBy('scheduled_at', 'desc')->limit(10)->get() as $appointment)
                                                            <tr>
                                                                <td>
                                                                    {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('patients.show', $appointment->patient) }}" class="contact-link">
                                                                        {{ $appointment->patient->getFullNameAttribute() }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span class="status-badge status-{{ $appointment->status }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $appointment->duration_minutes }} min</td>
                                                                <td>
                                                                    <a href="{{ route('appointments.show', $appointment) }}" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        Ver
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <a href="{{ route('appointments.index', ['doctor' => $doctor->id]) }}" class="btn btn-outline-primary">
                                                    Ver Todas las Citas
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <h5>Sin historial de citas</h5>
                                        <p>Este doctor aún no tiene citas registradas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function viewSchedule() {
    showGlobalAlert('Vista de agenda próximamente disponible', 'info');
}

function printDoctorInfo() {
    const printWindow = window.open('', '_blank');
    const doctorName = '{{ $doctor->getFullNameAttribute() }}';
    const specialty = '{{ $doctor->specialty }}';
    const licenseNumber = '{{ $doctor->license_number }}';
    const phone = '{{ $doctor->phone }}';
    const email = '{{ $doctor->email ?? "N/A" }}';
    const registrationDate = '{{ $doctor->created_at->format("d/m/Y") }}';
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Información del Doctor - ${doctorName}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info-table { width: 100%; border-collapse: collapse; }
                .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
                .info-table .label { font-weight: bold; width: 30%; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Información del Doctor</h1>
                <h2>${doctorName}</h2>
            </div>
            <table class="info-table">
                <tr><td class="label">Especialidad:</td><td>${specialty}</td></tr>
                <tr><td class="label">Cédula Profesional:</td><td>${licenseNumber}</td></tr>
                <tr><td class="label">Teléfono:</td><td>${phone}</td></tr>
                <tr><td class="label">Email:</td><td>${email}</td></tr>
                <tr><td class="label">Fecha de Registro:</td><td>${registrationDate}</td></tr>
                <tr><td class="label">Citas Totales:</td><td>{{ $totalAppointments }}</td></tr>
                <tr><td class="label">Citas Completadas:</td><td>{{ $completedAppointments }}</td></tr>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function deactivateDoctor(doctorId) {
    if (confirm('¿Está seguro de que desea desactivar este doctor?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/doctors/${doctorId}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush