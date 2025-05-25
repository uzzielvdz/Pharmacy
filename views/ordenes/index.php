<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Órdenes de Compra</h2>
<?php if (!empty($errors)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <strong>Error:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=create">Crear Nueva Orden</a>
<table>
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
            <tr>
                <td><?php echo htmlspecialchars($orden['id_orden']); ?></td>
                <td><?php echo htmlspecialchars($orden['proveedor']); ?></td>
                <td><?php echo htmlspecialchars($orden['fecha_orden']); ?></td>
                <td><?php echo htmlspecialchars($orden['estado']); ?></td>
                <td><?php echo htmlspecialchars($orden['total']); ?></td>
                <td>
                    <?php if ($orden['estado'] === 'pendiente'): ?>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=completar&id=<?php echo $orden['id_orden']; ?>" onclick="return confirm('¿Completar esta orden?');">Completar</a>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=cancelar&id=<?php echo $orden['id_orden']; ?>" onclick="return confirm('¿Cancelar esta orden?');">Cancelar</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=delete&id=<?php echo $orden['id_orden']; ?>" onclick="return confirm('¿Eliminar esta orden?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>