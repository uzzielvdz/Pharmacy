<?php include '../layouts/header.php'; ?>
<h2>Crear Producto</h2>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="POST" action="<?php echo BASE_URL; ?>/public/?action=create">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Descripción: <textarea name="descripcion"></textarea></label><br>
    <label>Stock: <input type="number" name="stock" required></label><br>
    <label>Precio: <input type="number" name="precio" step="0.01" required></label><br>
    <label>Fecha de Caducidad: <input type="date" name="fecha_caducidad"></label><br>
    <label>Proveedor: 
        <select name="id_proveedor">
            <option value="">Sin proveedor</option>
            <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?php echo $proveedor['id_proveedor']; ?>">
                    <?php echo htmlspecialchars($proveedor['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Crear</button>
</form>
<a href="<?php echo BASE_URL; ?>/public/">Volver</a>
<?php include '../layouts/footer.php'; ?>