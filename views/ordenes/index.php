<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2 class="text-2xl font-semibold mb-4">Órdenes de Compra</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate-slide-in" role="alert">
        <ul class="list-unstyled">
            <?php foreach ($errors as $error): ?>
                <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=create" class="btn btn-success mb-4">Crear Nueva Orden</a>
<?php if (empty($ordenes)): ?>
    <p class="text-gray-400">No hay órdenes registradas.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenes as $orden): ?>
                    <tr class="animate-fade-in">
                        <td><?php echo htmlspecialchars($orden['id_orden']); ?></td>
                        <td><?php echo htmlspecialchars($orden['proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($orden['fecha_orden']); ?></td>
                        <td><?php echo htmlspecialchars($orden['estado']); ?></td>
                        <td><?php echo htmlspecialchars($orden['total']); ?></td>
                        <td>
                            <?php if ($orden['estado'] === 'pendiente'): ?>
                                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=completar&id=<?php echo $orden['id_orden']; ?>" class="btn btn-success btn-sm">Completar</a>
                                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=cancelar&id=<?php echo $orden['id_orden']; ?>" class="btn btn-warning btn-sm">Cancelar</a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=delete&id=<?php echo $orden['id_orden']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>