{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Principal')

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
            <h2 class="page-title">Dashboard Principal</h2>
        </div>
        
        <div class="header-right">
            <div class="system-status">
                <div class="status-dot"></div>
                <span>Sistema Operativo</span>
            </div>
            
            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                @if($lowStockItems->count() > 0)
                    <span class="notification-badge">{{ $lowStockItems->count() }}</span>
                @endif
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <main class="dashboard-main" id="dashboardContent">
        
        <!-- KPI Cards Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card income-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="kpi-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12.5%</span>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <h3 class="kpi-value">${{ number_format($monthlyIncome, 0) }}</h3>
                        <p class="kpi-label">Ingresos del Mes</p>
                        <div class="kpi-subtitle">vs mes anterior</div>
                    </div>
                    <div class="kpi-chart">
                        <div class="mini-chart income-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card expense-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="kpi-trend negative">
                            <i class="fas fa-arrow-up"></i>
                            <span>+5.2%</span>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <h3 class="kpi-value">${{ number_format($monthlyExpenses, 0) }}</h3>
                        <p class="kpi-label">Gastos del Mes</p>
                        <div class="kpi-subtitle">vs mes anterior</div>
                    </div>
                    <div class="kpi-chart">
                        <div class="mini-chart expense-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card profit-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="kpi-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ $monthlyIncome > 0 ? number_format(($profit / $monthlyIncome) * 100, 1) : '0' }}%</span>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <h3 class="kpi-value">${{ number_format($profit, 0) }}</h3>
                        <p class="kpi-label">Utilidad Neta</p>
                        <div class="kpi-subtitle">margen de ganancia</div>
                    </div>
                    <div class="kpi-chart">
                        <div class="mini-chart profit-chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card appointments-card">
                    <div class="kpi-header">
                        <div class="kpi-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="kpi-metric">
                            <span>{{ $todayAppointments->count() }}</span>
                            <small>hoy</small>
                        </div>
                    </div>
                    <div class="kpi-body">
                        <h3 class="kpi-value">{{ $totalAppointments }}</h3>
                        <p class="kpi-label">Citas del Mes</p>
                        <div class="kpi-subtitle">{{ $completedAppointments }} completadas</div>
                    </div>
                    <div class="appointment-progress">
                        <div class="progress-ring" style="--progress: {{ $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0 }}%">
                            <span class="progress-text">{{ $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Widgets -->
        <div class="row g-4">
            
            <!-- Today's Schedule -->
            <div class="col-12 col-xl-8">
                <div class="dashboard-widget schedule-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-calendar-day widget-icon"></i>
                            <h5>Agenda de Hoy</h5>
                            <span class="widget-subtitle">{{ \Carbon\Carbon::today()->format('d M Y') }}</span>
                        </div>
                        <div class="widget-actions">
                            <button class="btn btn-primary btn-sm action-btn" data-section="appointments">
                                <i class="fas fa-plus me-1"></i>
                                Nueva Cita
                            </button>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        @if($todayAppointments->count() > 0)
                            <div class="appointments-timeline">
                                @foreach($todayAppointments as $appointment)
                                <div class="appointment-item {{ strtolower($appointment->status) }}">
                                    <div class="appointment-time">
                                        <span class="time">{{ $appointment->scheduled_at->format('H:i') }}</span>
                                        <span class="period">{{ $appointment->scheduled_at->format('A') }}</span>
                                    </div>
                                    
                                    <div class="appointment-details">
                                        <div class="patient-info">
                                            <div class="patient-avatar">
                                                {{ substr($appointment->patient->name, 0, 1) }}{{ substr($appointment->patient->last_name, 0, 1) }}
                                            </div>
                                            <div class="patient-data">
                                                <h6 class="patient-name">{{ $appointment->patient->full_name }}</h6>
                                                <p class="appointment-meta">
                                                    <i class="fas fa-user-md me-1"></i>{{ $appointment->doctor->full_name }}
                                                    <span class="separator">•</span>
                                                    <i class="fas fa-door-open me-1"></i>{{ $appointment->consultationRoom->name }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="appointment-status">
                                            <span class="status-badge status-{{ strtolower($appointment->status) }}">
                                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                            </span>
                                            <div class="appointment-actions">
                                                <button class="action-btn-sm" title="Ver detalles" 
                                                        onclick="viewAppointment('{{ $appointment->id }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn-sm" title="Editar"
                                                        onclick="editAppointment('{{ $appointment->id }}')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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
                                <h6>No hay citas programadas</h6>
                                <p>No tienes citas programadas para hoy</p>
                                <button class="btn btn-outline-primary btn-sm" data-section="appointments">
                                    <i class="fas fa-plus me-1"></i>Programar Cita
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Inventory Alerts & Quick Stats -->
            <div class="col-12 col-xl-4">
                <div class="row g-4">
                    
                    <!-- Stock Alerts -->
                    <div class="col-12">
                        <div class="dashboard-widget alert-widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <i class="fas fa-exclamation-triangle widget-icon warning"></i>
                                    <h5>Alertas de Stock</h5>
                                    @if($lowStockItems->count() > 0)
                                        <span class="alert-count">{{ $lowStockItems->count() }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="widget-body">
                                @if($lowStockItems->count() > 0)
                                    <div class="alert-items">
                                        @foreach($lowStockItems->take(4) as $item)
                                        <div class="alert-item">
                                            <div class="alert-icon">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div class="alert-content">
                                                <h6 class="item-name">{{ $item->name }}</h6>
                                                <p class="item-category">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</p>
                                            </div>
                                            <div class="alert-stock">
                                                <span class="current-stock">{{ $item->current_stock }}</span>
                                                <small class="min-stock">Mín: {{ $item->minimum_stock }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($lowStockItems->count() > 4)
                                    <div class="widget-footer">
                                        <a href="#inventory" class="view-all-link" data-section="inventory">
                                            Ver todos ({{ $lowStockItems->count() - 4 }} más)
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                    @endif
                                @else
                                    <div class="empty-state small">
                                        <div class="empty-icon success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <h6>Stock Saludable</h6>
                                        <p>Todos los productos tienen niveles adecuados</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-12">
                        <div class="dashboard-widget quick-actions-widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <i class="fas fa-bolt widget-icon"></i>
                                    <h5>Acciones Rápidas</h5>
                                </div>
                            </div>
                            
                            <div class="widget-body">
                                <div class="quick-actions-grid">
                                    <button class="quick-action-btn patient-btn" data-section="patients">
                                        <div class="action-icon">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                        <div class="action-text">
                                            <span>Nuevo</span>
                                            <small>Paciente</small>
                                        </div>
                                    </button>
                                    
                                    <button class="quick-action-btn appointment-btn" data-section="appointments">
                                        <div class="action-icon">
                                            <i class="fas fa-calendar-plus"></i>
                                        </div>
                                        <div class="action-text">
                                            <span>Agendar</span>
                                            <small>Cita</small>
                                        </div>
                                    </button>
                                    
                                    <button class="quick-action-btn invoice-btn" data-section="invoices">
                                        <div class="action-icon">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div class="action-text">
                                            <span>Facturar</span>
                                            <small>Servicios</small>
                                        </div>
                                    </button>
                                    
                                    <button class="quick-action-btn inventory-btn" data-section="inventory">
                                        <div class="action-icon">
                                            <i class="fas fa-plus-circle"></i>
                                        </div>
                                        <div class="action-text">
                                            <span>Agregar</span>
                                            <small>Stock</small>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Statistics and Charts Row -->
        <div class="row g-4 mt-4">
            
            <!-- Monthly Performance Chart -->
            <div class="col-12 col-lg-8">
                <div class="dashboard-widget chart-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-chart-area widget-icon"></i>
                            <h5>Rendimiento Mensual</h5>
                            <span class="widget-subtitle">Últimos 6 meses</span>
                        </div>
                        <div class="chart-controls">
                            <div class="btn-group chart-toggle" role="group">
                                <button class="btn btn-sm btn-outline-primary active" data-chart="income">Ingresos</button>
                                <button class="btn btn-sm btn-outline-primary" data-chart="expenses">Gastos</button>
                                <button class="btn btn-sm btn-outline-primary" data-chart="profit">Utilidad</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <div class="chart-placeholder">
                            <div class="chart-mockup">
                                <div class="chart-bars">
                                    <div class="chart-bar" style="height: 60%"></div>
                                    <div class="chart-bar" style="height: 75%"></div>
                                    <div class="chart-bar" style="height: 45%"></div>
                                    <div class="chart-bar" style="height: 85%"></div>
                                    <div class="chart-bar" style="height: 70%"></div>
                                    <div class="chart-bar" style="height: 90%"></div>
                                </div>
                                <div class="chart-overlay">
                                    <i class="fas fa-chart-line"></i>
                                    <p>Gráfico Interactivo</p>
                                    <small>Integrar con Chart.js</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Statistics -->
            <div class="col-12 col-lg-4">
                <div class="dashboard-widget stats-widget">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-chart-pie widget-icon"></i>
                            <h5>Estadísticas de Citas</h5>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <div class="stats-list">
                            <div class="stat-item">
                                <div class="stat-info">
                                    <span class="stat-label">Completadas</span>
                                    <span class="stat-value">{{ $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100) : 0 }}%</span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-progress completed" style="width: {{ $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-info">
                                    <span class="stat-label">Canceladas</span>
                                    <span class="stat-value">{{ $totalAppointments > 0 ? round(($cancelledAppointments / $totalAppointments) * 100) : 0 }}%</span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-progress cancelled" style="width: {{ $totalAppointments > 0 ? ($cancelledAppointments / $totalAppointments) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-info">
                                    <span class="stat-label">No Presentados</span>
                                    <span class="stat-value">3%</span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-progress no-show" style="width: 3%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-summary">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <h4>{{ $totalAppointments }}</h4>
                                    <span>Este Mes</span>
                                </div>
                                <div class="summary-item">
                                    <h4>{{ $todayAppointments->count() }}</h4>
                                    <span>Hoy</span>
                                </div>
                                <div class="summary-item">
                                    <h4>8</h4>
                                    <span>Mañana</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>



@endsection