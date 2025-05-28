<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar bg-dark text-white">
        <div class="sidebar-header p-3">
            <img src="<?= asset('img/logo.png') ?>" alt="Logo" class="img-fluid mb-3">
            <h5 class="text-center"><?= APP_NAME ?></h5>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=dashboard&action=index">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=product&action=index">
                        <i class="fas fa-pills"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=proveedor&action=index">
                        <i class="fas fa-truck"></i> Proveedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=orden&action=index">
                        <i class="fas fa-shopping-cart"></i> Órdenes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php?controller=movimiento&action=index">
                        <i class="fas fa-exchange-alt"></i> Movimientos
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-link" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="notificationsDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Notificaciones</h6>
                            <a class="dropdown-item" href="#">Nueva orden recibida</a>
                            <a class="dropdown-item" href="#">Stock bajo en producto</a>
                            <a class="dropdown-item" href="#">Actualización del sistema</a>
                        </div>
                    </div>
                    
                    <div class="dropdown ms-3">
                        <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <img src="<?= asset('img/user-avatar.png') ?>" alt="Usuario" class="rounded-circle" width="32">
                            <span class="ms-2"><?= $_SESSION['user_name'] ?? 'Usuario' ?></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="index.php?controller=user&action=profile">
                                <i class="fas fa-user"></i> Perfil
                            </a>
                            <a class="dropdown-item" href="index.php?controller=user&action=settings">
                                <i class="fas fa-cog"></i> Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="index.php?controller=auth&action=logout">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html> 