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
        /* ... tus estilos ... */
    </style>
</head>
<body>
<nav>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index">Productos</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=proveedor&action=index">Proveedores</a>
    <a href="<?php echo BASE_URL; ?>/public/index.php?controller=movimiento&action=index">Movimientos</a>
</nav>