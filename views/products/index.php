<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2 class="text-2xl font-semibold mb-4">Lista de Productos</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate-slide-in" role="alert">
        <ul class="list-unstyled">
            <?php foreach ($errors as $error): ?>
                <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=create" class="btn btn-success">Crear Nuevo Producto</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index" class="btn btn-secondary ms-2">Gestionar Proveedores</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index" class="btn btn-secondary ms-2">Gestionar Movimientos</a>
</div>
<?php if (empty($products)): ?>
    <p class="text-gray-400">No hay productos registrados.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Fecha de Caducidad</th>
                    <th>Proveedor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="animate-fade-in">
                        <td><?php echo htmlspecialchars($product['id_producto']); ?></td>
                        <td><?php echo htmlspecialchars($product['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($product['descripcion'] ?? 'Sin descripción'); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td><?php echo htmlspecialchars($product['precio']); ?></td>
                        <td><?php echo htmlspecialchars($product['fecha_caducidad'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($product['proveedor'] ?? 'Sin proveedor'); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=edit&id=<?php echo $product['id_producto']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=delete&id=<?php echo $product['id_producto']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>