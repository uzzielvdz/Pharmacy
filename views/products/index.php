<?php include '../layouts/header.php'; ?>
<h2>Lista de Productos</h2>
<a href="?action=create">Crear Nuevo Producto</a>
<?php if (empty($products)): ?>
    <p>No hay productos registrados.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Fecha de Caducidad</th>
                <th>Lote</th>
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
                    <td><?php echo htmlspecialchars($product['precio'] ?? '0.00'); ?></td>
                    <td><?php echo htmlspecialchars($product['fecha_caducidad'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['lote'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['proveedor'] ?? 'Sin proveedor'); ?></td>
                    <td>
                        <a href="?action=edit&id=<?php echo $product['id_producto']; ?>">Editar</a>
                        <a href="?action=delete&id=<?php echo $product['id_producto']; ?>" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include '../layouts/footer.php'; ?>