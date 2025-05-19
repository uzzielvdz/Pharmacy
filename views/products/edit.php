<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Editar Producto</h2>
<form action="?action=update" method="POST">
    <input type="hidden" name="id" value="<?php echo $product['id_producto']; ?>">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($product['nombre']); ?>" required><br>
    <label>Descripción:</label><br>
    <textarea name="descripcion"><?php echo htmlspecialchars($product['descripcion'] ?? ''); ?></textarea><br>
    <label>Precio:</label><br>
    <input type="number" name="precio" step="0.01" value="<?php echo $product['precio']; ?>" required><br>
    <label>Fecha de Caducidad:</label><br>
    <input type="date" name="fecha_caducidad" value="<?php echo $product['fecha_caducidad']; ?>" required><br>
    <label>Lote:</label><br>
    <input type="text" name="lote" value="<?php echo htmlspecialchars($product['lote']); ?>" required><br>
    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br>
    <label>Proveedor:</label><br>
    <select name="id_proveedor" required>
        <?php foreach ($proveedores as $proveedor): ?>
            <option value="<?php echo $proveedor['id_proveedor']; ?>" 
                    <?php echo $proveedor['id_proveedor'] == $product['id_proveedor'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($proveedor['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    <input type="submit" value="Actualizar">
    <a href="?action=index">Cancelar</a>
</form>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>