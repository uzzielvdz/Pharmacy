<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<div class="card bg-gray-700 animate-slide-in">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Crear Nuevo Proveedor</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate-slide-in" role="alert">
                <ul class="list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=store" method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo isset($formData['nombre']) ? htmlspecialchars($formData['nombre']) : ''; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contacto</label>
                <input type="text" name="contacto" class="form-control" value="<?php echo isset($formData['contacto']) ? htmlspecialchars($formData['contacto']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>" required>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>