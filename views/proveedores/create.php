<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<h2>Crear Nuevo Proveedor</h2>
<form action="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=store" method="POST">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br>
    <label>Contacto:</label><br>
    <input type="text" name="contacto"><br>
    <label>Email:</label><br>
    <input type="email" name="email"><br><br>
    <input type="submit" value="Guardar">
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Cancelar</a>
</form>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>
?>