@extends('layouts.app')

@section('title', 'Gestión de Pacientes')

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
            <h2 class="page-title">Gestión de Pacientes</h2>
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
        
        <!-- Page Header with Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-users widget-icon"></i>
                            <h5>Lista de Pacientes</h5>
                            <span class="widget-subtitle">{{ $patients->total() }} pacientes registrados</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nuevo Paciente
                            </a>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <!-- Filters Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" action="{{ route('patients.index') }}" class="patient-filters">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Buscar Paciente</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="search" 
                                                           value="{{ request('search') }}"
                                                           placeholder="Nombre, teléfono, email...">
                                                </div>
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Género</label>
                                                <select name="gender" class="form-select">
                                                    <option value="">Todos</option>
                                                    <option value="masculino" {{ request('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                                    <option value="femenino" {{ request('gender') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                                    <option value="otro" {{ request('gender') == 'otro' ? 'selected' : '' }}>Otro</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-filter me-1"></i>
                                                        Filtrar
                                                    </button>
                                                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
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

                        <!-- Patients Table -->
                        @if($patients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover patients-table">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Contacto</th>
                                        <th>Información</th>
                                        <th>Estado</th>
                                        <th>Registrado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patients as $patient)
                                    <tr class="patient-row">
                                        <td>
                                            <div class="patient-info">
                                                <div class="patient-avatar">
                                                    {{ substr($patient->name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                                </div>
                                                <div class="patient-details">
                                                    <h6 class="patient-name">{{ $patient->getFullNameAttribute() }}</h6>
                                                    @if($patient->birth_date)
                                                        <small class="text-muted">
                                                            {{ $patient->birth_date->age }} años • 
                                                            {{ ucfirst($patient->gender ?? 'No especificado') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div class="contact-item">
                                                    <i class="fas fa-phone text-primary"></i>
                                                    <span>{{ $patient->phone }}</span>
                                                </div>
                                                @if($patient->email)
                                                <div class="contact-item">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                    <span>{{ $patient->email }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="patient-metadata">
                                                @if($patient->rfc_nit)
                                                <div class="metadata-item">
                                                    <small class="text-muted">RFC/NIT:</small>
                                                    <span>{{ $patient->rfc_nit }}</span>
                                                </div>
                                                @endif
                                                <div class="metadata-item">
                                                    <small class="text-muted">Citas:</small>
                                                    <span class="badge bg-info">{{ $patient->appointments->count() }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($patient->is_active)
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
                                            <div class="date-info">
                                                <span>{{ $patient->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted d-block">{{ $patient->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('patients.show', $patient) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('patients.edit', $patient) }}" 
                                                   class="btn btn-outline-warning btn-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($patient->is_active)
                                                    <form method="POST" action="{{ route('patients.destroy', $patient) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                title="Desactivar"
                                                                onclick="return confirm('¿Estás seguro de desactivar este paciente?')">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('patients.activate', $patient) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-outline-success btn-sm" 
                                                                title="Activar"
                                                                onclick="return confirm('¿Estás seguro de activar este paciente?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($patients->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-info">
                                <small class="text-muted">
                                    Mostrando {{ $patients->firstItem() }} a {{ $patients->lastItem() }} 
                                    de {{ $patients->total() }} resultados
                                </small>
                            </div>
                            <div class="pagination-controls">
                                {{ $patients->links() }}
                            </div>
                        </div>
                        @endif

                        @else
                        <!-- Empty State -->
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h6>No hay pacientes registrados</h6>
                            <p>Comienza agregando tu primer paciente al sistema.</p>
                            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Agregar Primer Paciente
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality


    // Auto-submit form on filter change
    const filterSelects = document.querySelectorAll('select[name="status"], select[name="gender"]');
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