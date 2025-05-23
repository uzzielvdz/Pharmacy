<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Crear Orden de Compra</h2>
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
<form action="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=store" method="POST">
    <label>Proveedor:</label>
    <select name="id_proveedor" required>
        <option value="">Seleccione un proveedor</option>
        <?php foreach ($proveedores as $proveedor): ?>
            <option value="<?php echo $proveedor['id_proveedor']; ?>"><?php echo htmlspecialchars($proveedor['nombre']); ?></option>
        <?php endforeach; ?>
    </select><br>
    <label>Fecha:</label>
    <input type="datetime-local" name="fecha_orden" required><br>
    <h3>Detalles de la Orden</h3>
    <div id="detalles">
        <div class="detalle">
            <label>Producto:</label>
            <select name="productos[]">
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?php echo $producto['id_producto']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <label>Cantidad:</label>
            <input type="number" name="cantidades[]" min="1">
            <label>Precio Unitario:</label>
            <input type="number" name="precios[]" step="0.01" min="0">
        </div>
    </div>
    <button type="button" onclick="agregarDetalle()">Agregar Producto</button><br><br>
    <input type="submit" value="Crear Orden">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=index">Cancelar</a>
</form>
<script>
function agregarDetalle() {
    const detalles = document.getElementById('detalles');
    const nuevoDetalle = detalles.firstElementChild.cloneNode(true);
    nuevoDetalle.querySelector('select').value = '';
    nuevoDetalle.querySelector('input[type=number]').value = '';
    detalles.appendChild(nuevoDetalle);
}
</script>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>