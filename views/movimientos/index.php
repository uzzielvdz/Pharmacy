<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Lista de Movimientos</h2>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">Gestionar Productos</a><br><br>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=create">Registrar Nuevo Movimiento</a>
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
                <th>Acciones</th>
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
                    <td>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=edit&id=<?php echo $movimiento['id_movimiento']; ?>">Editar</a>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=delete&id=<?php echo $movimiento['id_movimiento']; ?>" onclick="return confirm('¿Estás seguro de eliminar este movimiento?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>