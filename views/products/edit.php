<?php include '../layouts/header.php'; ?>
<h2>Editar Producto</h2>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<?php if ($product): ?>
    <form method="POST" action="<?php echo BASE_URL; ?>/public/?action=edit&id=<?php echo $product['id_producto']; ?>">
        <label>Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($product['nombre']); ?>" required></label><br>
        <label>Descripción: <textarea name="descripcion"><?php echo htmlspecialchars($product['descripcion'] ?? ''); ?></textarea></label><br>
        <label>Stock: <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required></label><br>
        <label>Precio: <input type="number" name="precio" step="0.01" value="<?php echo $product['precio']; ?>" required></label><br>
        <label>Fecha de Caducidad: <input type="date" name="fecha_caducidad" value="<?php echo $product['fecha_caducidad'] ?? ''; ?>"></label><br>
        <label>Proveedor: 
            <select name="id_proveedor">
                <option value="">Sin proveedor</option>
                <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo $proveedor['id_proveedor']; ?>" <?php echo $proveedor['id_proveedor'] == $product['id_proveedor'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Actualizar</button>
    </form>
    <a href="<?php echo BASE_URL; ?>/public/">Volver</a>
<?php else: ?>
    <p>Producto no encontrado.</p>
<?php endif; ?>
<?php include '../layouts/footer.php'; ?>