@extends('layouts.app')
@section('title', 'Gestión de Facturas')
@section('content')
<div class="main-content">
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="page-title">Gestión de Facturas</h2>
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

    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Facturas</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="kpi-card income-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="kpi-metric"><span>{{ $totalInvoices }}</span><small>Total</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Facturas</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card profit-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="kpi-metric"><span>${{ number_format($totalAmount, 2) }}</span><small>Total</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Monto Facturado</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card appointments-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="kpi-metric"><span>${{ number_format($paidAmount, 2) }}</span><small>Pagado</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Total Cobrado</div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card expense-card">
                    <div class="kpi-header">
                        <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                        <div class="kpi-metric"><span>${{ number_format($pendingAmount, 2) }}</span><small>Pendiente</small></div>
                    </div>
                    <div class="kpi-body"><div class="kpi-label">Por Cobrar</div></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-file-invoice-dollar widget-icon"></i>
                            <h5>Lista de Facturas</h5>
                            <span class="widget-subtitle">{{ $invoices->total() }} facturas registradas</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Nueva Factura
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Filters -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Buscar factura o paciente..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="pagada" {{ request('status') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="payment_method" class="form-select">
                                    <option value="">Todos los métodos</option>
                                    <option value="efectivo" {{ request('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="transferencia" {{ request('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="credito" {{ request('payment_method') == 'credito' ? 'selected' : '' }}>Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
                            </div>
                        </form>

                        @if($invoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Paciente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Método Pago</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr>
                                                <td>
                                                    <strong>{{ $invoice->invoice_number }}</strong>
                                                    @if($invoice->appointment)
                                                        <small class="text-muted d-block">Cita médica</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('patients.show', $invoice->patient) }}" class="contact-link">
                                                        {{ $invoice->patient->getFullNameAttribute() }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="date-info">
                                                        <strong>{{ $invoice->created_at->format('d/m/Y') }}</strong>
                                                        <small class="text-muted d-block">{{ $invoice->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="text-success">${{ number_format($invoice->total, 2) }}</strong>
                                                    @if($invoice->tax > 0)
                                                        <small class="text-muted d-block">IVA: ${{ number_format($invoice->tax, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($invoice->status)
                                                        @case('pendiente')
                                                            <span class="status-badge status-pendiente">Pendiente</span>
                                                            @break
                                                        @case('pagada')
                                                            <span class="status-badge status-pagada">Pagada</span>
                                                            @break
                                                        @case('cancelada')
                                                            <span class="status-badge status-cancelada">Cancelada</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst($invoice->payment_method) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($invoice->status === 'pendiente')
                                                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        <button class="btn btn-outline-info btn-sm" onclick="generatePDF('{{ $invoice->id }}')" title="PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($invoice->status === 'pendiente')
                                                                    <li><button class="dropdown-item" onclick="updateStatus('{{ $invoice->id }}', 'pagada')">
                                                                        <i class="fas fa-check me-2"></i>Marcar como Pagada
                                                                    </button></li>
                                                                    <li><button class="dropdown-item" onclick="updateStatus('{{ $invoice->id }}', 'cancelada')">
                                                                        <i class="fas fa-times me-2"></i>Cancelar Factura
                                                                    </button></li>
                                                                @endif
                                                                <li><button class="dropdown-item" onclick="sendByEmail('{{ $invoice->id }}')">
                                                                    <i class="fas fa-envelope me-2"></i>Enviar por Email
                                                                </button></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $invoices->links() }}
                        @else
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                <h6>No hay facturas registradas</h6>
                                <p>Comience creando la primera factura.</p>
                                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Crear Primera Factura
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
function updateStatus(invoiceId, status) {
    const messages = {
        'pagada': '¿Marcar esta factura como pagada?',
        'cancelada': '¿Cancelar esta factura?'
    };
    
    if (confirm(messages[status])) {
        axios.patch(`/invoices/${invoiceId}/status`, { status })
        .then(response => {
            if (response.data.success) {
                showGlobalAlert(response.data.message, 'success');
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showGlobalAlert('Error al actualizar el estado', 'error');
        });
    }
}

function generatePDF(invoiceId) {
    window.open(`/invoices/${invoiceId}/pdf`, '_blank');
}

function sendByEmail(invoiceId) {
    const email = prompt('Ingrese el email de destino:');
    if (email) {
        showGlobalAlert('Enviando factura por email...', 'info');
        // Implement email sending
    }
}
</script>
@endpush
@endsection