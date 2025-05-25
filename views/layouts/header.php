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
            @apply bg-blue-900;
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/public/index.php">Farmacia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Proveedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">Movimientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=index">Órdenes</a>
                    </li>
                </ul>
                <button id="theme-toggle" class="btn btn-secondary ms-3">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
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