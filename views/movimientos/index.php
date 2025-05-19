<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Lista de Movimientos de Stock</h2>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=create">Registrar Nuevo Movimiento</a>
<?php if (empty($movimientos)): ?>
    <p>No hay movimientos registrados.</p>
<?php else: ?>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movimientos as $movimiento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($movimiento['id_movimiento']); ?></td>
                    <td><?php echo htmlspecialchars($movimiento['producto']); ?></td>
                    <td><?php echo htmlspecialchars($movimiento['tipo_movimiento']); ?></td>
                    <td><?php echo htmlspecialchars($movimiento['cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($movimiento['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($movimiento['motivo'] ?? 'Sin motivo'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>