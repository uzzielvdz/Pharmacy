<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Lista de Productos</h2>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Gestionar Proveedores</a><br>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">Gestionar Movimientos</a><br><br>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=create">Crear Nuevo Producto</a>
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
<?php if (empty($products)): ?>
    <p>No hay productos registrados.</p>
<?php else: ?>
    <table border="1">
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
                <tr>
                    <td><?php echo htmlspecialchars($product['id_producto']); ?></td>
                    <td><?php echo htmlspecialchars($product['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($product['descripcion'] ?? 'Sin descripción'); ?></td>
                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                    <td><?php echo htmlspecialchars($product['precio']); ?></td>
                    <td><?php echo htmlspecialchars($product['fecha_caducidad'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['proveedor'] ?? 'Sin proveedor'); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=edit&id=<?php echo $product['id_producto']; ?>">Editar</a>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=delete&id=<?php echo $product['id_producto']; ?>" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>