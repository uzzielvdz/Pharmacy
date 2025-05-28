<?php
$title = 'Detalles del Producto';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Producto</h1>
        <div>
            <a href="<?= url('products') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?= url('product/edit/' . $product['id_producto']) ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <!-- Product Details -->
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?= asset('img/products/' . ($product['imagen'] ?? 'default.jpg')) ?>" 
                         alt="<?= $product['nombre'] ?>" 
                         class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <h4><?= $product['nombre'] ?></h4>
                    <span class="badge bg-<?= $product['stock'] > 10 ? 'success' : 
                        ($product['stock'] > 0 ? 'warning' : 'danger') ?>">
                        <?= $product['stock'] > 10 ? 'En stock' : 
                            ($product['stock'] > 0 ? 'Stock bajo' : 'Agotado') ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Información del Producto</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Categoría:</strong> <?= ucfirst($product['categoria']) ?></p>
                            <p><strong>Precio:</strong> $<?= number_format($product['precio'], 2) ?></p>
                            <p><strong>Stock:</strong> <?= $product['stock'] ?> unidades</p>
                            <p><strong>Proveedor:</strong> <?= $product['proveedor'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Lote:</strong> <?= $product['lote'] ?></p>
                            <p><strong>Fecha de Caducidad:</strong> <?= $product['fecha_caducidad'] ?></p>
                            <p><strong>ID del Producto:</strong> <?= $product['id_producto'] ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6>Descripción</h6>
                        <p><?= nl2br($product['descripcion']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Stock History -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Historial de Stock</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($product['movimientos']) && !empty($product['movimientos'])): ?>
                                    <?php foreach ($product['movimientos'] as $movimiento): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $movimiento['tipo_movimiento'] === 'entrada' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($movimiento['tipo_movimiento']) ?>
                                                </span>
                                            </td>
                                            <td><?= $movimiento['cantidad'] ?></td>
                                            <td><?= $movimiento['motivo'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No hay movimientos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?> 