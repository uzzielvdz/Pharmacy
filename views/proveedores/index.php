<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2 class="text-2xl font-semibold mb-4">Lista de Proveedores</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate-slide-in" role="alert">
        <ul class="list-unstyled">
            <?php foreach ($errors as $error): ?>
                <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=create" class="btn btn-success mb-4">Crear Nuevo Proveedor</a>
<?php if (empty($proveedores)): ?>
    <p class="text-gray-400">No hay proveedores registrados.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proveedores as $proveedor): ?>
                    <tr class="animate-fade-in">
                        <td><?php echo htmlspecialchars($proveedor['id_proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['contacto'] ?? 'Sin contacto'); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['email'] ?? 'Sin email'); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=edit&id=<?php echo $proveedor['id_proveedor']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=delete&id=<?php echo $proveedor['id_proveedor']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>