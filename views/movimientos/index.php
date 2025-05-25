<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2 class="text-2xl font-semibold mb-4">Lista de Movimientos</h2>
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
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=create" class="btn btn-success">Registrar Nuevo Movimiento</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index" class="btn btn-secondary ms-2">Gestionar Productos</a>
</div>
<?php if (empty($movimientos)): ?>
    <p class="text-gray-400">No hay movimientos registrados.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr class="animate-fade-in">
                        <td><?php echo htmlspecialchars($movimiento['id_movimiento']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['producto']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['tipo_movimiento']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['motivo'] ?? 'Sin motivo'); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=edit&id=<?php echo $movimiento['id_movimiento']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=delete&id=<?php echo $movimiento['id_movimiento']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>