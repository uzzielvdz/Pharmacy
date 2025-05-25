<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<div class="card bg-gray-700 animate-slide-in">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Registrar Nuevo Movimiento</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate-slide-in" role="alert">
                <ul class="list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=store" method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Producto</label>
                <select name="id_producto" class="form-select" required>
                    <option value="">Seleccione un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?php echo $producto['id_producto']; ?>" <?php echo (isset($formData['id_producto']) && $formData['id_producto'] == $producto['id_producto']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipo de Movimiento</label>
                <select name="tipo_movimiento" class="form-select" required>
                    <option value="entrada" <?php echo (isset($formData['tipo_movimiento']) && $formData['tipo_movimiento'] == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                    <option value="salida" <?php echo (isset($formData['tipo_movimiento']) && $formData['tipo_movimiento'] == 'salida') ? 'selected' : ''; ?>>Salida</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" min="1" class="form-control" value="<?php echo isset($formData['cantidad']) ? htmlspecialchars($formData['cantidad']) : ''; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="datetime-local" name="fecha" class="form-control" value="<?php echo isset($formData['fecha']) ? date('Y-m-d\TH:i', strtotime($formData['fecha'])) : ''; ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Motivo</label>
                <input type="text" name="motivo" maxlength="100" class="form-control" value="<?php echo isset($formData['motivo']) ? htmlspecialchars($formData['motivo']) : ''; ?>">
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Registrar</button>
                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>