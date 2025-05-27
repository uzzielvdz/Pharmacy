<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, Arial, sans-serif;
            @apply bg-gray-800 text-gray-100;
        }
        .navbar {
            @apply bg-blue-900 shadow-lg;
        }
        .table thead {
            @apply bg-blue-900 text-white;
        }
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-500 transition-transform transform hover:scale-105;
        }
        .btn-success {
            @apply bg-green-600 hover:bg-green-500 transition-transform transform hover:scale-105;
        }
        .btn-danger {
            @apply bg-red-600 hover:bg-red-500 transition-transform transform hover:scale-105;
        }
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-500 transition-transform transform hover:scale-105;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-in;
        }
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .nav-link {
            @apply relative text-blue-100 hover:text-white;
        }
        .nav-link::after {
            content: '';
            @apply absolute bottom-0 left-0 w-0 h-0.5 bg-blue-400 transition-all duration-300;
        }
        .nav-link:hover::after {
            @apply w-full;
        }
        .navbar-brand {
            @apply flex items-center space-x-2 text-xl font-semibold text-white hover:text-blue-200;
        }
        .notification-badge {
            @apply absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center;
        }
        .dropdown-menu {
            @apply bg-blue-900 border border-blue-800;
        }
        .dropdown-item {
            @apply text-blue-100 hover:bg-blue-800;
        }
        .dropdown-divider {
            @apply border-blue-800;
        }
        .btn-link {
            @apply text-blue-100 hover:text-white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/public/index.php">
                <i class="bi bi-capsule text-blue-300"></i>
                <span>Farmacia</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">
                            <i class="bi bi-box me-1"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">
                            <i class="bi bi-truck me-1"></i>Proveedores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">
                            <i class="bi bi-arrow-left-right me-1"></i>Movimientos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=index">
                            <i class="bi bi-cart me-1"></i>Órdenes
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <!-- Reportes -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-graph-up me-1"></i>Reportes
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header text-white">Reportes</h6>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-graph-up me-2"></i>Stock Bajo
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-calendar-check me-2"></i>Por Vencer
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-cart me-2"></i>Ventas
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-file-earmark-text me-2"></i>Auditoría
                            </a>
                        </div>
                    </div>
                    <!-- Notificaciones -->
                    <div class="position-relative me-3">
                        <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header text-white">Notificaciones</h6>
                            <a class="dropdown-item" href="#">
                                <small class="text-blue-300">Stock Bajo</small>
                                <p class="mb-0">5 productos con stock bajo</p>
                            </a>
                            <a class="dropdown-item" href="#">
                                <small class="text-yellow-400">Por Vencer</small>
                                <p class="mb-0">3 productos próximos a vencer</p>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center text-blue-300" href="#">Ver todas</a>
                        </div>
                    </div>
                    <!-- Usuario -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>Usuario
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header text-white">Mi Cuenta</h6>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-red-400" href="#">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </div>
                    </div>
                    <!-- Tema -->
                    <button id="theme-toggle" class="btn btn-secondary">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="container my-4 animate-fade-in">
    <script>
        // Theme toggle
        document.getElementById('theme-toggle').addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const icon = document.querySelector('#theme-toggle i');
            icon.classList.toggle('bi-moon-stars-fill');
            icon.classList.toggle('bi-sun-fill');
        });
        // SweetAlert2 for delete confirmations
        document.querySelectorAll('a[onclick*="confirm"]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location = link.href;
                    }
                });
            });
        });
    </script>