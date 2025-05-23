<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Editar Producto</h2>
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
<form action="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=update" method="POST">
    <input type="hidden" name="id" value="<?php echo isset($formData['id_producto']) ? htmlspecialchars($formData['id_producto']) : ''; ?>">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo isset($formData['nombre']) ? htmlspecialchars($formData['nombre']) : ''; ?>" required><br>
    <label>Descripción:</label><br>
    <textarea name="descripcion"><?php echo isset($formData['descripcion']) ? htmlspecialchars($formData['descripcion']) : ''; ?></textarea><br>
    <label>Stock:</label><br>
    <input type="number" name="stock" min="0" value="<?php echo isset($formData['stock']) ? htmlspecialchars($formData['stock']) : 0; ?>" required><br>
    <label>Precio:</label><br>
    <input type="number" name="precio" step="0.01" min="0" value="<?php echo isset($formData['precio']) ? htmlspecialchars($formData['precio']) : 0; ?>" required><br>
    <label>Fecha de Caducidad:</label><br>
    <input type="date" name="fecha_caducidad" value="<?php echo isset($formData['fecha_caducidad']) ? htmlspecialchars($formData['fecha_caducidad']) : ''; ?>"><br>
    <label>Lote:</label><br>
    <input type="text" name="lote" value="<?php echo isset($formData['lote']) ? htmlspecialchars($formData['lote']) : ''; ?>"><br>
    <label>Proveedor:</label><br>
    <select name="id_proveedor" required>
        <option value="">Seleccione un proveedor</option>
        <?php foreach ($proveedores as $proveedor): ?>
            <option value="<?php echo $proveedor['id_proveedor']; ?>" <?php echo (isset($formData['id_proveedor']) && $formData['id_proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($proveedor['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>
    <input type="submit" value="Actualizar Producto">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">Cancelar</a>
</form>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>