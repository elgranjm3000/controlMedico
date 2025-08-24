@extends('layouts.app')

@section('title', 'Paciente: ' . $patient->getFullNameAttribute())

@section('content')
<!-- Sidebar -->


<!-- Main Content -->
<div class="main-content">
    <!-- Top Header -->

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
                            <a href="{{ route('patients.index') }}"><i class="fas fa-users me-1"></i>Pacientes</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-user me-1"></i>{{ $patient->getFullNameAttribute() }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Patient Header Card -->
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
                                            {{ substr($patient->name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                            <div class="status-indicator {{ $patient->is_active ? 'online' : 'offline' }}"></div>
                                        </div>
                                        <div class="patient-details-large">
                                            <h1 class="patient-name-large">
                                                {{ $patient->getFullNameAttribute() }}
                                                @if(!$patient->is_active)
                                                    <span class="badge bg-warning text-dark ms-2">
                                                        <i class="fas fa-pause"></i> Inactivo
                                                    </span>
                                                @endif
                                            </h1>
                                            <div class="patient-meta-large">
                                                @if($patient->birth_date)
                                                    <span class="meta-item">
                                                        <i class="fas fa-birthday-cake"></i>
                                                        {{ $patient->birth_date->age }} años
                                                        <small class="text-muted">({{ $patient->birth_date->format('d/m/Y') }})</small>
                                                    </span>
                                                @endif
                                                @if($patient->gender)
                                                    <span class="meta-item">
                                                        <i class="fas fa-{{ $patient->gender === 'masculino' ? 'mars' : ($patient->gender === 'femenino' ? 'venus' : 'genderless') }}"></i>
                                                        {{ ucfirst($patient->gender) }}
                                                    </span>
                                                @endif
                                                <span class="meta-item">
                                                    <i class="fas fa-calendar-plus"></i>
                                                    Registrado {{ $patient->created_at->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="patient-actions">
                                        <div class="action-group">
                                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-light btn-action">
                                                <i class="fas fa-edit"></i>
                                                <span>Editar</span>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle btn-action" type="button" 
                                                        id="patientActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="patientActionsDropdown">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}">
                                                            <i class="fas fa-calendar-plus me-2"></i>Nueva Cita
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}">
                                                            <i class="fas fa-file-invoice me-2"></i>Crear Factura
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="printPatientInfo()">
                                                            <i class="fas fa-print me-2"></i>Imprimir Info
                                                        </button>
                                                    </li>
                                                    @if($patient->is_active)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-warning" onclick="deactivatePatient('{{ $patient->id }}')">
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

        <!-- Patient Details Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-info-tab" data-bs-toggle="tab" 
                                        data-bs-target="#nav-info" type="button" role="tab">
                                    <i class="fas fa-info-circle me-2"></i>Información General
                                </button>
                                <button class="nav-link" id="nav-appointments-tab" data-bs-toggle="tab" 
                                        data-bs-target="#nav-appointments" type="button" role="tab">
                                    <i class="fas fa-calendar-alt me-2"></i>Citas
                                </button>
                                <button class="nav-link" id="nav-financial-tab" data-bs-toggle="tab" 
                                        data-bs-target="#nav-financial" type="button" role="tab">
                                    <i class="fas fa-money-bill-wave me-2"></i>Financiero
                                </button>
                                <button class="nav-link" id="nav-medical-tab" data-bs-toggle="tab" 
                                        data-bs-target="#nav-medical" type="button" role="tab">
                                    <i class="fas fa-file-medical-alt me-2"></i>Médico
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
                                                <h5><i class="fas fa-user me-2"></i>Información Personal</h5>
                                            </div>
                                            <div class="info-card-body">
                                                <div class="info-list">
                                                    @if($patient->birth_date)
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-birthday-cake"></i>
                                                            Fecha de Nacimiento:
                                                        </span>
                                                        <span class="info-value">
                                                            {{ $patient->birth_date->format('d/m/Y') }}
                                                            <small class="text-muted">({{ $patient->birth_date->age }} años)</small>
                                                        </span>
                                                    </div>
                                                    @endif
                                                    @if($patient->gender)
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-venus-mars"></i>
                                                            Género:
                                                        </span>
                                                        <span class="info-value">{{ ucfirst($patient->gender) }}</span>
                                                    </div>
                                                    @endif
                                                    @if($patient->rfc_nit)
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-id-card"></i>
                                                            RFC/NIT:
                                                        </span>
                                                        <span class="info-value">{{ $patient->rfc_nit }}</span>
                                                    </div>
                                                    @endif
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-calendar-plus"></i>
                                                            Registrado:
                                                        </span>
                                                        <span class="info-value">
                                                            {{ $patient->created_at->format('d/m/Y H:i') }}
                                                            <small class="text-muted">({{ $patient->created_at->diffForHumans() }})</small>
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
                                                            <a href="tel:{{ $patient->phone }}" class="contact-link">
                                                                {{ $patient->phone }}
                                                                <i class="fas fa-external-link-alt ms-1"></i>
                                                            </a>
                                                        </span>
                                                    </div>
                                                    @if($patient->email)
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-envelope"></i>
                                                            Email:
                                                        </span>
                                                        <span class="info-value">
                                                            <a href="mailto:{{ $patient->email }}" class="contact-link">
                                                                {{ $patient->email }}
                                                                <i class="fas fa-external-link-alt ms-1"></i>
                                                            </a>
                                                        </span>
                                                    </div>
                                                    @endif
                                                    @if($patient->address)
                                                    <div class="info-item">
                                                        <span class="info-label">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            Dirección:
                                                        </span>
                                                        <span class="info-value">
                                                            {{ $patient->address }}
                                                            <button class="btn btn-sm btn-outline-primary ms-2" 
                                                                    onclick="openMaps('{{ urlencode($patient->address) }}')">
                                                                <i class="fas fa-map"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Appointments History Tab -->
                            <div class="tab-pane fade" id="nav-appointments" role="tabpanel">
                                @if($patient->appointments->count() > 0)
                                    <div class="appointments-timeline">
                                        @foreach($patient->appointments->sortByDesc('scheduled_at')->take(10) as $appointment)
                                        <div class="timeline-item appointment-timeline-item status-{{ $appointment->status }}">
                                            <div class="timeline-marker">
                                                <i class="fas fa-{{ $appointment->status === 'completada' ? 'check' : ($appointment->status === 'cancelada' ? 'times' : 'clock') }}"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-header">
                                                    <div class="timeline-date">
                                                        <strong>{{ $appointment->scheduled_at->format('d/m/Y') }}</strong>
                                                        <span class="timeline-time">{{ $appointment->scheduled_at->format('H:i') }}</span>
                                                    </div>
                                                    <span class="status-badge status-{{ $appointment->status }}">
                                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                                    </span>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="appointment-details">
                                                        <p class="mb-2">
                                                            <i class="fas fa-user-md me-2"></i>
                                                            <strong>{{ $appointment->doctor->getFullNameAttribute() }}</strong>
                                                            <span class="text-muted">- {{ $appointment->doctor->specialty }}</span>
                                                        </p>
                                                        <p class="mb-2">
                                                            <i class="fas fa-door-open me-2"></i>
                                                            {{ $appointment->consultationRoom->name }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <i class="fas fa-clock me-2"></i>
                                                            Duración: {{ $appointment->duration_minutes }} minutos
                                                        </p>
                                                        @if($appointment->notes)
                                                        <div class="appointment-notes mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-sticky-note me-1"></i>
                                                                {{ $appointment->notes }}
                                                            </small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <h5>Sin historial de citas</h5>
                                        <p>Este paciente aún no tiene citas registradas.</p>
                                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            Programar Primera Cita
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Financial Information Tab -->
                            <div class="tab-pane fade" id="nav-financial" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="financial-summary-card">
                                            <div class="financial-item">
                                                <div class="financial-icon bg-primary">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </div>
                                                <div class="financial-content">
                                                    <div class="financial-label">Total Facturado</div>
                                                    <div class="financial-value text-primary">
                                                        ${{ number_format($patient->invoices->sum('total'), 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="financial-item">
                                                <div class="financial-icon bg-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="financial-content">
                                                    <div class="financial-label">Total Pagado</div>
                                                    <div class="financial-value text-success">
                                                        ${{ number_format($patient->invoices->where('status', 'pagada')->sum('total'), 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="financial-item">
                                                <div class="financial-icon bg-warning">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="financial-content">
                                                    <div class="financial-label">Pendiente</div>
                                                    <div class="financial-value text-warning">
                                                        ${{ number_format($patient->invoices->where('status', 'pendiente')->sum('total'), 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-8">
                                        @if($patient->invoices->count() > 0)
                                            <div class="invoices-list">
                                                <div class="invoices-header">
                                                    <h6><i class="fas fa-file-invoice me-2"></i>Historial de Facturas</h6>
                                                    <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-plus me-1"></i>Nueva Factura
                                                    </a>
                                                </div>
                                                
                                                @foreach($patient->invoices->sortByDesc('created_at')->take(5) as $invoice)
                                                <div class="invoice-card">
                                                    <div class="invoice-header">
                                                        <div class="invoice-number">
                                                            <strong>{{ $invoice->invoice_number }}</strong>
                                                            <span class="invoice-date">{{ $invoice->created_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        <span class="status-badge status-{{ $invoice->status }}">
                                                            {{ ucfirst($invoice->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="invoice-body">
                                                        <div class="invoice-amount">
                                                            <span class="amount-label">Total:</span>
                                                            <span class="amount-value">${{ number_format($invoice->total, 2) }}</span>
                                                        </div>
                                                        <div class="invoice-method">
                                                            <small class="text-muted">
                                                                <i class="fas fa-credit-card me-1"></i>
                                                                {{ ucfirst($invoice->payment_method) }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="invoice-actions">
                                                        <a href="{{ route('invoices.show', $invoice) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($invoice->status === 'pendiente')
                                                            <a href="{{ route('invoices.edit', $invoice) }}" 
                                                               class="btn btn-sm btn-outline-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <i class="fas fa-file-invoice"></i>
                                                </div>
                                                <h5>Sin facturas</h5>
                                                <p>Este paciente no tiene facturas registradas.</p>
                                                <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Crear Primera Factura
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information Tab -->
                            <div class="tab-pane fade" id="nav-medical" role="tabpanel">
                                @if($patient->medical_notes)
                                    <div class="medical-notes-card">
                                        <div class="medical-notes-header">
                                            <h5><i class="fas fa-file-medical-alt me-2"></i>Notas Médicas</h5>
                                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </a>
                                        </div>
                                        <div class="medical-notes-body">
                                            <div class="notes-content">{{ $patient->medical_notes }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-file-medical-alt"></i>
                                        </div>
                                        <h5>Sin información médica</h5>
                                        <p>Este paciente no tiene notas médicas registradas.</p>
                                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>
                                            Agregar Notas Médicas
                                        </a>
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
 
function openMaps(address) {
    const url = `https://www.google.com/maps/search/?api=1&query=${address}`;
    window.open(url, '_blank');
}

function printPatientInfo() {
    const printWindow = window.open('', '_blank');
    const patientName = '{{ $patient->getFullNameAttribute() }}';
    const patientPhone = '{{ $patient->phone }}';
    const patientEmail = '{{ $patient->email ?? "N/A" }}';
const patientAddress = '{{ str_replace(["\r", "\n"], ' ', $patient->address ?? "N/A") }}';
    const registrationDate = '{{ $patient->created_at->format("d/m/Y") }}';
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Información del Paciente - ${patientName}</title>
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
                <h1>Información del Paciente</h1>
                <h2>${patientName}</h2>
            </div>
            <table class="info-table">
                <tr><td class="label">Teléfono:</td><td>${patientPhone}</td></tr>
                <tr><td class="label">Email:</td><td>${patientEmail}</td></tr>
                <tr><td class="label">Dirección:</td><td>${patientAddress}</td></tr>
                <tr><td class="label">Fecha de Registro:</td><td>${registrationDate}</td></tr>
            </table>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function deactivatePatient(patientId) {
    if (confirm('¿Está seguro de que desea desactivar este paciente?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/patients/${patientId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        // Add method field for DELETE
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
