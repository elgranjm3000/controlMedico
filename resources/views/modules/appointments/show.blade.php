@extends('layouts.app')

@section('title', 'Cita: ' . $appointment->patient->getFullNameAttribute())

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
            <h2 class="page-title">Detalles de Cita</h2>
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
                            <a href="{{ route('appointments.index') }}"><i class="fas fa-calendar-alt me-1"></i>Citas</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-info-circle me-1"></i>Detalles
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Appointment Header Card -->
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
                                            <i class="fas fa-calendar-check"></i>
                                            <div class="status-indicator {{ $appointment->status === 'completada' ? 'online' : ($appointment->status === 'cancelada' ? 'offline' : 'online') }}"></div>
                                        </div>
                                        <div class="patient-details-large">
                                            <h1 class="patient-name-large">
                                                Cita Médica
                                                @switch($appointment->status)
                                                    @case('programada')
                                                        <span class="badge bg-warning text-dark ms-2">
                                                            <i class="fas fa-clock"></i> Programada
                                                        </span>
                                                        @break
                                                    @case('confirmada')
                                                        <span class="badge bg-info ms-2">
                                                            <i class="fas fa-check"></i> Confirmada
                                                        </span>
                                                        @break
                                                    @case('en_curso')
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-play"></i> En Curso
                                                        </span>
                                                        @break
                                                    @case('completada')
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-check-circle"></i> Completada
                                                        </span>
                                                        @break
                                                    @case('cancelada')
                                                        <span class="badge bg-danger ms-2">
                                                            <i class="fas fa-times-circle"></i> Cancelada
                                                        </span>
                                                        @break
                                                @endswitch
                                            </h1>
                                            <div class="patient-meta-large">
                                                <span class="meta-item">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $appointment->scheduled_at->format('d/m/Y') }}
                                                    <small class="text-muted">({{ $appointment->scheduled_at->diffForHumans() }})</small>
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-clock"></i>
                                                    
                                                    {{ $appointment->scheduled_at->copy()->addMinutes((int)$appointment->duration_minutes)->format('H:i') }}

                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-stopwatch"></i>
                                                    {{ $appointment->duration_minutes }} minutos
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="patient-actions">
                                        <div class="action-group">
                                            @if($appointment->status !== 'completada' && $appointment->status !== 'cancelada')
                                                <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-outline-light btn-action">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Editar</span>
                                                </a>
                                            @endif
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle btn-action" type="button" 
                                                    id="appointmentActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="appointmentActionsDropdown">
                                                    @if($appointment->status === 'programada')
                                                        <li>
                                                            <button class="dropdown-item" onclick="updateStatus('confirmada')">
                                                                <i class="fas fa-check me-2"></i>Confirmar Cita
                                                            </button>
                                                        </li>
                                                    @endif
                                                    @if($appointment->status === 'confirmada')
                                                        <li>
                                                            <button class="dropdown-item" onclick="updateStatus('en_curso')">
                                                                <i class="fas fa-play me-2"></i>Iniciar Cita
                                                            </button>
                                                        </li>
                                                    @endif
                                                    @if($appointment->status === 'en_curso')
                                                        <li>
                                                            <button class="dropdown-item" onclick="updateStatus('completada')">
                                                                <i class="fas fa-check-circle me-2"></i>Completar Cita
                                                            </button>
                                                        </li>
                                                    @endif
                                                    @if(!$appointment->invoice && $appointment->status === 'completada')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('invoices.create', ['appointment_id' => $appointment->id]) }}">
                                                                <i class="fas fa-file-invoice me-2"></i>Crear Factura
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="printAppointmentInfo()">
                                                            <i class="fas fa-print me-2"></i>Imprimir Info
                                                        </button>
                                                    </li>
                                                    @if($appointment->status !== 'cancelada' && $appointment->status !== 'completada')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-warning" onclick="updateStatus('cancelada')">
                                                                <i class="fas fa-times me-2"></i>Cancelar Cita
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

        <!-- Appointment Details -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-user me-2"></i>Información del Paciente</h5>
                    </div>
                    <div class="info-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user"></i>
                                    Nombre:
                                </span>
                                <span class="info-value">
                                    <a href="{{ route('patients.show', $appointment->patient) }}" class="contact-link">
                                        {{ $appointment->patient->getFullNameAttribute() }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-phone"></i>
                                    Teléfono:
                                </span>
                                <span class="info-value">
                                    <a href="tel:{{ $appointment->patient->phone }}" class="contact-link">
                                        {{ $appointment->patient->phone }}
                                    </a>
                                </span>
                            </div>
                            @if($appointment->patient->email)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-envelope"></i>
                                        Email:
                                    </span>
                                    <span class="info-value">
                                        <a href="mailto:{{ $appointment->patient->email }}" class="contact-link">
                                            {{ $appointment->patient->email }}
                                        </a>
                                    </span>
                                </div>
                            @endif
                            @if($appointment->patient->birth_date)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-birthday-cake"></i>
                                        Edad:
                                    </span>
                                    <span class="info-value">{{ $appointment->patient->birth_date->age }} años</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-user-md me-2"></i>Información del Doctor</h5>
                    </div>
                    <div class="info-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user-md"></i>
                                    Doctor:
                                </span>
                                <span class="info-value">
                                    <a href="{{ route('doctors.show', $appointment->doctor) }}" class="contact-link">
                                        {{ $appointment->doctor->getFullNameAttribute() }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-stethoscope"></i>
                                    Especialidad:
                                </span>
                                <span class="info-value">{{ $appointment->doctor->specialty }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-id-card"></i>
                                    Cédula:
                                </span>
                                <span class="info-value">{{ $appointment->doctor->license_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-phone"></i>
                                    Teléfono:
                                </span>
                                <span class="info-value">
                                    <a href="tel:{{ $appointment->doctor->phone }}" class="contact-link">
                                        {{ $appointment->doctor->phone }}
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-door-open me-2"></i>Información de la Consulta</h5>
                    </div>
                    <div class="info-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-door-open"></i>
                                    Sala de Consulta:
                                </span>
                                <span class="info-value">{{ $appointment->consultationRoom->name }}</span>
                            </div>
                            @if($appointment->consultationRoom->location)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Ubicación:
                                    </span>
                                    <span class="info-value">{{ $appointment->consultationRoom->location }}</span>
                                </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar-plus"></i>
                                    Creada:
                                </span>
                                <span class="info-value">
                                    {{ $appointment->created_at->format('d/m/Y H:i') }}
                                    <small class="text-muted">({{ $appointment->created_at->diffForHumans() }})</small>
                                </span>
                            </div>
                            @if($appointment->updated_at != $appointment->created_at)
                                <div class="info-item">
                                    <span class="info-label">
                                        <i class="fas fa-edit"></i>
                                        Última actualización:
                                    </span>
                                    <span class="info-value">
                                        {{ $appointment->updated_at->format('d/m/Y H:i') }}
                                        <small class="text-muted">({{ $appointment->updated_at->diffForHumans() }})</small>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <h5><i class="fas fa-sticky-note me-2"></i>Notas y Observaciones</h5>
                    </div>
                    <div class="info-card-body">
                        @if($appointment->notes)
                            <div class="notes-content">
                                {{ $appointment->notes }}
                            </div>
                        @else
                            <div class="empty-state small">
                                <div class="empty-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                <p>No hay notas para esta cita</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Information -->
        @if($appointment->invoice)
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="fas fa-file-invoice me-2"></i>Información de Facturación</h5>
                        </div>
                        <div class="info-card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Número de Factura:</strong>
                                    <br>
                                    <a href="{{ route('invoices.show', $appointment->invoice) }}" class="contact-link">
                                        {{ $appointment->invoice->invoice_number }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <strong>Total:</strong>
                                    <br>
                                    <span class="text-success">${{ number_format($appointment->invoice->total, 2) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong>
                                    <br>
                                    @switch($appointment->invoice->status)
                                        @case('pendiente')
                                            <span class="status-badge status-pendiente">
                                                <i class="fas fa-clock"></i>
                                                Pendiente
                                            </span>
                                            @break
                                        @case('pagada')
                                            <span class="status-badge status-pagada">
                                                <i class="fas fa-check-circle"></i>
                                                Pagada
                                            </span>
                                            @break
                                        @case('cancelada')
                                            <span class="status-badge status-cancelada">
                                                <i class="fas fa-times-circle"></i>
                                                Cancelada
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                                <div class="col-md-3">
                                    <strong>Fecha:</strong>
                                    <br>
                                    {{ $appointment->invoice->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(status) {
    const statusMessages = {
        'confirmada': '¿Confirmar esta cita?',
        'en_curso': '¿Iniciar esta cita?',
        'completada': '¿Marcar esta cita como completada?',
        'cancelada': '¿Cancelar esta cita?'
    };

    if (confirm(statusMessages[status])) {
        axios.patch(`/appointments/{{ $appointment->id }}/status`, {
            status: status
        })
        .then(response => {
            if (response.data.success) {
                showGlobalAlert(response.data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showGlobalAlert('Error al actualizar el estado de la cita', 'error');
        });
    }
}

function printAppointmentInfo() {
    const printWindow = window.open('', '_blank');
    const appointmentDate = '{{ $appointment->scheduled_at->format("d/m/Y H:i") }}';
    const patientName = '{{ $appointment->patient->getFullNameAttribute() }}';
    const doctorName = '{{ $appointment->doctor->getFullNameAttribute() }}';
    const roomName = '{{ $appointment->consultationRoom->name }}';
    const duration = '{{ $appointment->duration_minutes }}';
    const status = '{{ ucfirst($appointment->status) }}';
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Cita Médica - ${patientName}</title>
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
                <h1>Información de Cita Médica</h1>
                <h2>${patientName}</h2>
            </div>
            <table class="info-table">
                <tr><td class="label">Fecha y Hora:</td><td>${appointmentDate}</td></tr>
                <tr><td class="label">Paciente:</td><td>${patientName}</td></tr>
                <tr><td class="label">Doctor:</td><td>${doctorName}</td></tr>
                <tr><td class="label">Sala:</td><td>${roomName}</td></tr>
                <tr><td class="label">Duración:</td><td>${duration} minutos</td></tr>
                <tr><td class="label">Estado:</td><td>${status}</td></tr>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush