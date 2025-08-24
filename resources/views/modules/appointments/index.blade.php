@extends('layouts.app')

@section('title', 'Gestión de Citas')

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
            <h2 class="page-title">Gestión de Citas</h2>
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
                        <li class="breadcrumb-item active">Citas</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Page Header with Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-calendar-alt widget-icon"></i>
                            <h5>Lista de Citas</h5>
                            <span class="widget-subtitle">{{ $appointments->total() }} citas registradas</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nueva Cita
                            </a>
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleCalendarView()">
                                <i class="fas fa-calendar me-1"></i>
                                Vista Calendario
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Filters Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('appointments.index') }}" class="patient-filters">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Buscar</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        name="search" 
                                                        value="{{ request('search') }}"
                                                        placeholder="Paciente o doctor...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Estado</label>
                                                <select name="status" class="form-select">
                                                    <option value="">Todos</option>
                                                    <option value="programada" {{ request('status') == 'programada' ? 'selected' : '' }}>Programada</option>
                                                    <option value="confirmada" {{ request('status') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                                    <option value="en_curso" {{ request('status') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                                    <option value="completada" {{ request('status') == 'completada' ? 'selected' : '' }}>Completada</option>
                                                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Doctor</label>
                                                <select name="doctor" class="form-select">
                                                    <option value="">Todos</option>
                                                    @foreach($doctors as $doctor)
                                                        <option value="{{ $doctor->id }}" {{ request('doctor') == $doctor->id ? 'selected' : '' }}>
                                                            {{ $doctor->getFullNameAttribute() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Desde</label>
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="date_from" 
                                                    value="{{ request('date_from') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Hasta</label>
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="date_to" 
                                                    value="{{ request('date_to') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-filter"></i>
                                                    </button>
                                                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Appointments Table -->
                        @if($appointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover appointments-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Paciente</th>
                                            <th>Doctor</th>
                                            <th>Sala</th>
                                            <th>Estado</th>
                                            <th>Duración</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointments as $appointment)
                                            <tr class="appointment-row">
                                                <td>
                                                    <div class="appointment-time">
                                                        <strong>{{ $appointment->scheduled_at->format('d/m/Y') }}</strong>
                                                        <div class="text-muted">{{ $appointment->scheduled_at->format('H:i') }}</div>
                                                        @if($appointment->scheduled_at->isToday())
                                                            <small class="badge bg-info">Hoy</small>
                                                        @elseif($appointment->scheduled_at->isTomorrow())
                                                            <small class="badge bg-warning">Mañana</small>
                                                        @elseif($appointment->scheduled_at->isPast())
                                                            <small class="badge bg-secondary">Pasada</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="patient-info">
                                                        <div class="patient-avatar">
                                                            {{ substr($appointment->patient->name, 0, 1) }}{{ substr($appointment->patient->last_name, 0, 1) }}
                                                        </div>
                                                        <div class="patient-details">
                                                            <h6 class="patient-name">{{ $appointment->patient->getFullNameAttribute() }}</h6>
                                                            <small class="text-muted">{{ $appointment->patient->phone }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="doctor-info">
                                                        <strong>{{ $appointment->doctor->getFullNameAttribute() }}</strong>
                                                        <div class="text-muted">{{ $appointment->doctor->specialty }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="room-badge">
                                                        <i class="fas fa-door-open me-1"></i>
                                                        {{ $appointment->consultationRoom->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @switch($appointment->status)
                                                        @case('programada')
                                                            <span class="status-badge status-programada">
                                                                <i class="fas fa-clock"></i>
                                                                Programada
                                                            </span>
                                                            @break
                                                        @case('confirmada')
                                                            <span class="status-badge status-confirmada">
                                                                <i class="fas fa-check"></i>
                                                                Confirmada
                                                            </span>
                                                            @break
                                                        @case('en_curso')
                                                            <span class="status-badge status-en_curso">
                                                                <i class="fas fa-play"></i>
                                                                En Curso
                                                            </span>
                                                            @break
                                                        @case('completada')
                                                            <span class="status-badge status-completada">
                                                                <i class="fas fa-check-circle"></i>
                                                                Completada
                                                            </span>
                                                            @break
                                                        @case('cancelada')
                                                            <span class="status-badge status-cancelada">
                                                                <i class="fas fa-times-circle"></i>
                                                                Cancelada
                                                            </span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="duration-info">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $appointment->duration_minutes }} min
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('appointments.show', $appointment) }}" 
                                                            class="btn btn-outline-primary btn-sm" 
                                                            title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($appointment->status !== 'completada' && $appointment->status !== 'cancelada')
                                                            <a href="{{ route('appointments.edit', $appointment) }}" 
                                                                class="btn btn-outline-warning btn-sm" 
                                                                title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                type="button" 
                                                                data-bs-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($appointment->status === 'programada')
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="updateStatus('{{ $appointment->id }}', 'confirmada')">
                                                                            <i class="fas fa-check me-2"></i>Confirmar
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                                @if($appointment->status === 'confirmada')
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="updateStatus('{{ $appointment->id }}', 'en_curso')">
                                                                            <i class="fas fa-play me-2"></i>Iniciar
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                                @if($appointment->status === 'en_curso')
                                                                    <li>
                                                                        <button class="dropdown-item" onclick="updateStatus('{{ $appointment->id }}', 'completada')">
                                                                            <i class="fas fa-check-circle me-2"></i>Completar
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
                                                                @if($appointment->status !== 'cancelada' && $appointment->status !== 'completada')
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <button class="dropdown-item text-danger" onclick="updateStatus('{{ $appointment->id }}', 'cancelada')">
                                                                            <i class="fas fa-times me-2"></i>Cancelar
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($appointments->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="pagination-info">
                                        <small class="text-muted">
                                            Mostrando {{ $appointments->firstItem() }} a {{ $appointments->lastItem() }} 
                                            de {{ $appointments->total() }} resultados
                                        </small>
                                    </div>
                                    <div class="pagination-controls">
                                        {{ $appointments->links() }}
                                    </div>
                                </div>
                            @endif

                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <h6>No hay citas registradas</h6>
                                <p>Comienza programando la primera cita médica.</p>
                                <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Programar Primera Cita
                                </a>
                            </div>
                        @endif
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
    // Auto-submit form on filter change
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="doctor"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Handle search input with debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
});

function updateStatus(appointmentId, status) {
    const statusMessages = {
        'confirmada': '¿Confirmar esta cita?',
        'en_curso': '¿Iniciar esta cita?',
        'completada': '¿Marcar esta cita como completada?',
        'cancelada': '¿Cancelar esta cita?'
    };

    if (confirm(statusMessages[status])) {
        axios.patch(`/appointments/${appointmentId}/status`, {
            status: status
        })
        .then(response => {
            if (response.data.success) {
                showToast(response.data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al actualizar el estado de la cita', 'error');
        });
    }
}

function toggleCalendarView() {
    // Placeholder for calendar view toggle
    showToast('Vista calendario próximamente disponible', 'info');
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