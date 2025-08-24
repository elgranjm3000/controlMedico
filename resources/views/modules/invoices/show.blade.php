@extends('layouts.app')
@section('title', 'Factura: ' . $invoice->invoice_number)
@section('content')
<div class="main-content">
    <main class="dashboard-main">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                        <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-file-invoice-dollar widget-icon"></i>
                            <h5>Factura {{ $invoice->invoice_number }}</h5>
                            <span class="widget-subtitle">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="widget-actions">
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            @if($invoice->status === 'pendiente')
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                            @endif
                            <button class="btn btn-outline-info btn-sm" onclick="generatePDF('{{ $invoice->id }}')">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </button>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- Invoice Header -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <h6>Información del Paciente</h6>
                                    </div>
                                    <div class="info-card-body">
                                        <strong>{{ $invoice->patient->getFullNameAttribute() }}</strong><br>
                                        {{ $invoice->patient->phone }}<br>
                                        @if($invoice->patient->email)
                                            {{ $invoice->patient->email }}<br>
                                        @endif
                                        @if($invoice->patient->address)
                                            {{ $invoice->patient->address }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-header">
                                        <h6>Detalles de la Factura</h6>
                                    </div>
                                    <div class="info-card-body">
                                        <strong>Estado:</strong> 
                                        <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span><br>
                                        <strong>Método de Pago:</strong> {{ ucfirst($invoice->payment_method) }}<br>
                                        @if($invoice->due_date)
                                            <strong>Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}<br>
                                        @endif
                                        @if($invoice->appointment)
                                            <strong>Cita:</strong> {{ $invoice->appointment->scheduled_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="row">
                            <div class="col-12">
                                <h6><i class="fas fa-list me-2"></i>Servicios y Productos</h6>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-end">Precio Unit.</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $item)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $item->description }}</strong>
                                                        @if($item->service)
                                                            <small class="text-muted d-block">Servicio Médico</small>
                                                        @elseif($item->inventoryItem)
                                                            <small class="text-muted d-block">Producto: {{ $item->inventoryItem->code }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-end"><strong>${{ number_format($item->total, 2) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                <td class="text-end"><strong>${{ number_format($invoice->subtotal, 2) }}</strong></td>
                                            </tr>
                                            @if($invoice->tax > 0)
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>IVA (16%):</strong></td>
                                                    <td class="text-end"><strong>${{ number_format($invoice->tax, 2) }}</strong></td>
                                                </tr>
                                            @endif
                                            <tr class="table-primary">
                                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                <td class="text-end"><strong>${{ number_format($invoice->total, 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($invoice->notes)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-sticky-note me-2"></i>Notas</h6>
                                        {{ $invoice->notes }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection