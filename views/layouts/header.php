<?php
defined('BASE_URL') || define('BASE_URL', 'http://localhost/Pharmacy/public');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Farmacia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        a { color: #007bff; text-decoration: none; margin-right: 10px; }
        a:hover { text-decoration: underline; }
        form { max-width: 600px; margin-top: 20px; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        input[type="submit"] { background-color: #007bff; color: white; border: none; padding: 10px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<nav>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">Productos</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Proveedores</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">Movimientos</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=index">Órdenes</a>
</nav>