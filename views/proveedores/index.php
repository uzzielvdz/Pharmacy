<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Lista de Proveedores</h2>
<?php if (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>
<a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=create">Crear Nuevo Proveedor</a>
<?php if (empty($proveedores)): ?>
    <p>No hay proveedores registrados.</p>
<?php else: ?>
    <table border="1">
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
                <tr>
                    <td><?php echo htmlspecialchars($proveedor['id_proveedor']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['contacto'] ?? 'Sin contacto'); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['email'] ?? 'Sin email'); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=edit&id=<?php echo $proveedor['id_proveedor']; ?>">Editar</a>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=delete&id=<?php echo $proveedor['id_proveedor']; ?>" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>