{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'ClinicaPro') - Sistema de Gestión Clínica</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha512-b2QcS5SsA8tZodcDtGRELiGv5SaKSk1vDHDaQRda0htPYWZ6046lr3kJ5bAAQdpV2mmA/4v0wQF9MyU6/pDIAg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Dashboard CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Sistema de Gestión Integral para Clínicas Médicas - ClinicaPro">
    <meta name="keywords" content="clinica, gestion, medico, citas, pacientes, facturacion">
    <meta name="author" content="ClinicaPro">
    
    <!-- Additional head content -->
    @stack('styles')
</head>
<body class="@yield('body-class', '')">
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-overlay">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="loading-text">Cargando ClinicaPro...</div>
        </div>
    </div>

    <!-- Main Application Content -->
    <div id="app" class="d-none">
        @yield('content')
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999;" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999;" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999;" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999;" role="alert">
            <i class="fas fa-times-circle me-2"></i>
            <strong>¡Oops! Hay algunos errores:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js" 
            integrity="sha512-X/YkDZyjTf4wyc2Vy16YGCPHwAY8rZJY+POgokZjQB2mhIRFJCckEGc6YyX9eNsPfn0PzThEuNs+uaomE5CO6A==" 
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <!-- Axios for AJAX requests -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js" 
            integrity="sha512-b94Z6431JyXY14iSXwgzeZurHHRNkLt9d6bAHt7BZT38eqV+GyngIi/tVye4jBKPYQ2lBdRs0glww4fmpuLRwA==" 
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Global JavaScript -->
    <script>
        // Global configuration
        window.App = {
            baseURL: '{{ url('/') }}',
            csrfToken: '{{ csrf_token() }}',
            user: @json(auth()->user() ?? null),
            locale: '{{ app()->getLocale() }}',
            timezone: 'America/Mexico_City'
        };

        // Configure Axios defaults
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.baseURL = window.App.baseURL;

        // Global error handling
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response?.status === 419) {
                    // CSRF token mismatch
                    window.location.reload();
                } else if (error.response?.status === 401) {
                    // Unauthorized
                    window.location.href = '/login';
                } else if (error.response?.status === 403) {
                    // Forbidden
                    showGlobalAlert('No tienes permisos para realizar esta acción', 'error');
                }
                return Promise.reject(error);
            }
        );

        // Global alert function
        function showGlobalAlert(message, type = 'info', duration = 5000) {
            const alertTypes = {
                success: { icon: 'check-circle', class: 'alert-success' },
                error: { icon: 'exclamation-circle', class: 'alert-danger' },
                warning: { icon: 'exclamation-triangle', class: 'alert-warning' },
                info: { icon: 'info-circle', class: 'alert-info' }
            };

            const alertConfig = alertTypes[type] || alertTypes.info;
            
            const alertElement = document.createElement('div');
            alertElement.className = `alert ${alertConfig.class} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertElement.style.zIndex = '9999';
            alertElement.innerHTML = `
                <i class="fas fa-${alertConfig.icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            document.body.appendChild(alertElement);

            // Auto dismiss after duration
            setTimeout(() => {
                if (alertElement.parentNode) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, duration);
        }

        // Global loading functions
        function showGlobalLoading() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.classList.remove('d-none');
                spinner.classList.add('d-flex');
            }
        }

        function hideGlobalLoading() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.classList.add('d-none');
                spinner.classList.remove('d-flex');
            }
        }

        // Format currency function
        function formatCurrency(amount, currency = 'MXN') {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2
            }).format(amount);
        }

        // Format date function
        function formatDate(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            };
            
            return new Date(date).toLocaleDateString('es-MX', { ...defaultOptions, ...options });
        }

        // Confirm action function
        function confirmAction(message, callback, title = '¿Estás seguro?') {
            if (confirm(`${title}\n\n${message}`)) {
                callback();
            }
        }

        // Initialize application
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading spinner and show app
            hideGlobalLoading();
            document.getElementById('app').classList.remove('d-none');

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });

            // Add smooth scrolling to anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Handle form submissions with loading states
            document.querySelectorAll('form[data-loading]').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                        
                        // Re-enable after 10 seconds as fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 10000);
                    }
                });
            });

            // Add current year to copyright
            const currentYearElements = document.querySelectorAll('[data-current-year]');
            currentYearElements.forEach(element => {
                element.textContent = new Date().getFullYear();
            });

            console.log('ClinicaPro Dashboard initialized successfully');
        });

        // Handle navigation state
        function setActiveNavigation(section) {
            // Remove active class from all nav items
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to current section
            const activeLink = document.querySelector(`[data-section="${section}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // Handle online/offline status
        window.addEventListener('online', function() {
            showGlobalAlert('Conexión restablecida', 'success', 3000);
        });

        window.addEventListener('offline', function() {
            showGlobalAlert('Sin conexión a internet', 'warning', 0);
        });
    </script>

    <!-- Additional JavaScript -->
    @stack('scripts')
</body>
</html>

<style>
/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.spinner-container {
    text-align: center;
    color: white;
}

.spinner-container .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 4px;
}

.loading-text {
    margin-top: 1rem;
    font-size: 1.1rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
}

/* Custom Alert Styles */
.alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.alert-success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9));
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9));
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(217, 119, 6, 0.9));
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9));
    color: white;
}

.alert .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.alert .btn-close:hover {
    opacity: 1;
}

/* Custom animations */
@keyframes slideInDown {
    from {
        transform: translateY(-100%) translateX(-50%);
        opacity: 0;
    }
    to {
        transform: translateY(0) translateX(-50%);
        opacity: 1;
    }
}

.alert.position-fixed {
    animation: slideInDown 0.3s ease-out;
    max-width: 500px;
    min-width: 300px;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .alert.position-fixed {
        max-width: 90vw;
        left: 5vw !important;
        transform: none !important;
    }
    
    .loading-text {
        font-size: 1rem;
    }
    
    .spinner-container .spinner-border {
        width: 2.5rem;
        height: 2.5rem;
    }
}
</style>