@extends('layouts.app')

@section('title', 'Gestión de Doctores')

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
            <h2 class="page-title">Gestión de Doctores</h2>
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
                        <li class="breadcrumb-item active">Doctores</li>
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
                            <i class="fas fa-user-md widget-icon"></i>
                            <h5>Lista de Doctores</h5>
                            <span class="widget-subtitle">{{ $doctors->total() }} doctores registrados</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('doctors.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nuevo Doctor
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Filters Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('doctors.index') }}" class="doctor-filters">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Buscar Doctor</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        name="search" 
                                                        value="{{ request('search') }}"
                                                        placeholder="Nombre, especialidad, cédula...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Especialidad</label>
                                                <select name="specialty" class="form-select">
                                                    <option value="">Todas</option>
                                                    @foreach($specialties as $specialty)
                                                        <option value="{{ $specialty }}" {{ request('specialty') == $specialty ? 'selected' : '' }}>
                                                            {{ $specialty }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Estado</label>
                                                <select name="status" class="form-select">
                                                    <option value="">Todos</option>
                                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivos</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-filter me-1"></i>
                                                        Filtrar
                                                    </button>
                                                    <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-times me-1"></i>
                                                        Limpiar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Doctors Table -->
                        @if($doctors->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover doctors-table">
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Especialidad</th>
                                            <th>Contacto</th>
                                            <th>Cédula</th>
                                            <th>Estado</th>
                                            <th>Citas</th>
                                            <th>Registrado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($doctors as $doctor)
                                            <tr class="doctor-row">
                                                <td>
                                                    <div class="doctor-info">
                                                        <div class="doctor-avatar">
                                                            {{ substr($doctor->name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}
                                                        </div>
                                                        <div class="doctor-details">
                                                            <h6 class="doctor-name">{{ $doctor->getFullNameAttribute() }}</h6>
                                                            <small class="text-muted">ID: {{ substr($doctor->id, 0, 8) }}...</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="specialty-info">
                                                        <span class="specialty-badge">
                                                            <i class="fas fa-stethoscope me-1"></i>
                                                            {{ $doctor->specialty }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="contact-info">
                                                        <div class="contact-item">
                                                            <i class="fas fa-phone text-primary"></i>
                                                            <span>{{ $doctor->phone }}</span>
                                                        </div>
                                                        @if($doctor->email)
                                                            <div class="contact-item">
                                                                <i class="fas fa-envelope text-muted"></i>
                                                                <span>{{ $doctor->email }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="license-number">
                                                        <i class="fas fa-id-card me-1"></i>
                                                        {{ $doctor->license_number }}
                                                    </span>
                                                </td>
                                                <td>
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
                                                </td>
                                                <td>
                                                    <div class="appointments-count">
                                                        <span class="badge bg-info">
                                                            {{ $doctor->appointments()->count() }}
                                                        </span>
                                                        <small class="text-muted d-block">citas totales</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="date-info">
                                                        <span>{{ $doctor->created_at->format('d/m/Y') }}</span>
                                                        <small class="text-muted d-block">{{ $doctor->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('doctors.show', $doctor) }}" 
                                                            class="btn btn-outline-primary btn-sm" 
                                                            title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('doctors.edit', $doctor) }}" 
                                                            class="btn btn-outline-warning btn-sm" 
                                                            title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($doctor->is_active)
                                                            <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm" 
                                                                    title="Desactivar"
                                                                    onclick="return confirm('¿Estás seguro de desactivar este doctor?')">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form method="POST" action="{{ route('doctors.activate', $doctor) }}" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" 
                                                                    class="btn btn-outline-success btn-sm" 
                                                                    title="Activar"
                                                                    onclick="return confirm('¿Estás seguro de activar este doctor?')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                                type="button" 
                                                                data-bs-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}">
                                                                        <i class="fas fa-calendar-plus me-2"></i>Nueva Cita
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item" onclick="viewSchedule('{{ $doctor->id }}')">
                                                                        <i class="fas fa-calendar me-2"></i>Ver Agenda
                                                                    </button>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <button class="dropdown-item" onclick="exportDoctorData('{{ $doctor->id }}')">
                                                                        <i class="fas fa-download me-2"></i>Exportar Datos
                                                                    </button>
                                                                </li>
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
                            @if($doctors->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="pagination-info">
                                        <small class="text-muted">
                                            Mostrando {{ $doctors->firstItem() }} a {{ $doctors->lastItem() }} 
                                            de {{ $doctors->total() }} resultados
                                        </small>
                                    </div>
                                    <div class="pagination-controls">
                                        {{ $doctors->links() }}
                                    </div>
                                </div>
                            @endif

                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <h6>No hay doctores registrados</h6>
                                <p>Comienza agregando el primer doctor al sistema.</p>
                                <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Agregar Primer Doctor
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
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="specialty"]');
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

function viewSchedule(doctorId) {
    // Placeholder for schedule view
    showToast('Agenda del doctor próximamente disponible', 'info');
}

function exportDoctorData(doctorId) {
    showToast('Exportando datos del doctor...', 'info');
    // Placeholder for export functionality
    setTimeout(() => {
        showToast('Exportación completada', 'success');
    }, 2000);
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