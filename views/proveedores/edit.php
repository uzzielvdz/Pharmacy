<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Editar Proveedor</h2>
<form action="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=update" method="POST">
    <input type="hidden" name="id" value="<?php echo $proveedor['id_proveedor']; ?>">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" required><br>
    <label>Contacto:</label><br>
    <input type="text" name="contacto" value="<?php echo htmlspecialchars($proveedor['contacto'] ?? ''); ?>"><br>
    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($proveedor['email'] ?? ''); ?>"><br><br>
    <input type="submit" value="Actualizar">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Cancelar</a>
</form>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>