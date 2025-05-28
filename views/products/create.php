<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<div class="card bg-gray-700 animate-slide-in">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Crear Nuevo Producto</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate-slide-in" role="alert">
                <ul class="list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=store" method="POST" class="row g-3" enctype="multipart/form-data">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo isset($formData['nombre']) ? htmlspecialchars($formData['nombre']) : ''; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Precio</label>
                <input type="number" name="precio" min="0.01" step="0.01" class="form-control" value="<?php echo isset($formData['precio']) ? htmlspecialchars($formData['precio']) : ''; ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control"><?php echo isset($formData['descripcion']) ? htmlspecialchars($formData['descripcion']) : ''; ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" min="0" class="form-control" value="<?php echo isset($formData['stock']) ? htmlspecialchars($formData['stock']) : '0'; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha de Caducidad</label>
                <input type="date" name="fecha_caducidad" class="form-control" value="<?php echo isset($formData['fecha_caducidad']) ? htmlspecialchars($formData['fecha_caducidad']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Lote</label>
                <input type="text" name="lote" class="form-control" value="<?php echo isset($formData['lote']) ? htmlspecialchars($formData['lote']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Proveedor</label>
                <select name="id_proveedor" class="form-select" required>
                    <option value="">Seleccione un proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo $proveedor['id_proveedor']; ?>" <?php echo (isset($formData['id_proveedor']) && $formData['id_proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($proveedor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select" required>
                    <option value="medicamentos" <?php echo (isset($formData['categoria']) && $formData['categoria'] == 'medicamentos') ? 'selected' : ''; ?>>Medicamentos</option>
                    <option value="equipamiento" <?php echo (isset($formData['categoria']) && $formData['categoria'] == 'equipamiento') ? 'selected' : ''; ?>>Equipamiento</option>
                    <option value="suministros" <?php echo (isset($formData['categoria']) && $formData['categoria'] == 'suministros') ? 'selected' : ''; ?>>Suministros</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Imagen del Producto</label>
                <input type="file" class="form-control" name="imagen" accept="image/*">
                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Crear</button>
                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>