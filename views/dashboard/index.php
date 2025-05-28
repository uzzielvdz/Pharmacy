<?php
$title = 'Dashboard';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="btn-group">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <button class="btn btn-outline-secondary" onclick="exportarDashboard()">
                <i class="fas fa-file-export"></i> Exportar
            </button>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">¡Atención!</h4>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Productos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_productos'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Productos Bajo Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['productos_bajo_stock'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Productos Caducados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['productos_caducados'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Órdenes Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['ordenes_pendientes'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Productos Bajo Stock -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Productos con Bajo Stock</h6>
                    <a href="index.php?controller=product" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosBajoStock as $producto): ?>
                                <tr>
                                    <td><?= $producto['nombre'] ?></td>
                                    <td><?= $producto['stock'] ?></td>
                                    <td><?= isset($producto['stock_minimo']) ? $producto['stock_minimo'] : '10' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos Próximos a Caducar -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Productos Próximos a Caducar</h6>
                    <a href="index.php?controller=product" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Fecha Caducidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosProximosCaducar as $producto): ?>
                                <tr>
                                    <td><?= $producto['nombre'] ?></td>
                                    <td><?= $producto['stock'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($producto['fecha_caducidad'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Movimientos Recientes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Movimientos Recientes</h6>
                    <a href="index.php?controller=movimiento" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientosRecientes as $movimiento): ?>
                                <tr>
                                    <td><?= $movimiento['producto'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $movimiento['tipo_movimiento'] === 'entrada' ? 'success' : 
                                            ($movimiento['tipo_movimiento'] === 'salida' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($movimiento['tipo_movimiento']) ?>
                                        </span>
                                    </td>
                                    <td><?= $movimiento['cantidad'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Órdenes Pendientes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Órdenes Pendientes</h6>
                    <a href="index.php?controller=orden" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proveedor</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordenesPendientes as $orden): ?>
                                <tr>
                                    <td><?= $orden['id_orden'] ?></td>
                                    <td><?= $orden['proveedor'] ?></td>
                                    <td>$<?= number_format($orden['total'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($orden['fecha_orden'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportarDashboard() {
    // Implementar exportación a Excel o PDF
    alert('Función de exportación en desarrollo');
}
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?> 