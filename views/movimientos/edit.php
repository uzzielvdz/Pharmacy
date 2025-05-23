<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Editar Movimiento</h2>
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
<form action="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=update" method="POST">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($formData['id_movimiento']); ?>">
    <label>Producto:</label><br>
    <select name="id_producto" required>
        <option value="">Seleccione un producto</option>
        <?php foreach ($productos as $producto): ?>
            <option value="<?php echo $producto['id_producto']; ?>" <?php echo (isset($formData['id_producto']) && $formData['id_producto'] == $producto['id_producto']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($producto['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <label>Tipo de Movimiento:</label><br>
    <select name="tipo_movimiento" required>
        <option value="entrada" <?php echo (isset($formData['tipo_movimiento']) && $formData['tipo_movimiento'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
        <option value="salida" <?php echo (isset($formData['tipo_movimiento']) && $formData['tipo_movimiento'] == 'salida') ? 'selected' : ''; ?>>Salida</option>
    </select><br>
    <label>Cantidad:</label><br>
    <input type="number" name="cantidad" min="1" value="<?php echo isset($formData['cantidad']) ? htmlspecialchars($formData['cantidad']) : ''; ?>" required><br>
    <label>Fecha:</label><br>
    <input type="datetime-local" name="fecha" value="<?php 
        if (isset($formData['fecha'])) {
            $fecha = new DateTime($formData['fecha']);
            echo $fecha->format('Y-m-d\TH:i');
        }
    ?>" required><br>
    <label>Motivo:</label><br>
    <input type="text" name="motivo" maxlength="100" value="<?php echo isset($formData['motivo']) ? htmlspecialchars($formData['motivo']) : ''; ?>"><br><br>
    <input type="submit" value="Actualizar">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">Cancelar</a>
</form>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>