<?php
require_once '../models/Product.php';

try {
    $productModel = new Product();

    // 1. Probar getAll()
    echo "<h2>1. Lista de todos los productos (getAll):</h2>";
    $products = $productModel->getAll();
    if ($products) {
        echo "<pre>";
        print_r($products);
        echo "</pre>";
    } else {
        echo "No se encontraron productos.";
    }

    // 2. Probar getById()
    $testId = 1; // ID de Paracetamol 500mg
    echo "<h2>2. Producto con ID $testId (getById):</h2>";
    $product = $productModel->getById($testId);
    if ($product) {
        echo "<pre>";
        print_r($product);
        echo "</pre>";
    } else {
        echo "Producto con ID $testId no encontrado.";
    }

    // 3. Probar create()
    echo "<h2>3. Crear un nuevo producto (create):</h2>";
    $newProduct = [
        'nombre' => 'Aspirina 100mg',
        'descripcion' => 'Analgésico',
        'precio' => 4.99,
        'fecha_caducidad' => '2026-06-30',
        'lote' => 'LOTE789',
        'stock' => 75,
        'id_proveedor' => 1 // Laboratorios ACME
    ];
    if ($productModel->create($newProduct)) {
        echo "Producto creado exitosamente. Verifica la lista de productos:<br>";
        $productsAfterCreate = $productModel->getAll();
        echo "<pre>";
        print_r($productsAfterCreate);
        echo "</pre>";
    } else {
        echo "Error al crear el producto.";
    }

    // 4. Probar update()
    echo "<h2>4. Actualizar producto con ID $testId (update):</h2>";
    $updateData = [
        'nombre' => 'Paracetamol 500mg Actualizado',
        'descripcion' => 'Analgésico (actualizado)',
        'precio' => 16.00,
        'fecha_caducidad' => '2025-12-31',
        'lote' => 'LOTE123_MOD',
        'stock' => 120,
        'id_proveedor' => 1 // Mantener Laboratorios ACME
    ];
    if ($productModel->update($testId, $updateData)) {
        echo "Producto actualizado exitosamente:<br>";
        $updatedProduct = $productModel->getById($testId);
        echo "<pre>";
        print_r($updatedProduct);
        echo "</pre>";
    } else {
        echo "Error al actualizar el producto.";
    }
    
    // 5. Probar delete()
echo "<h2>5. Eliminar producto con ID 4 (delete):</h2>";
$deleteId = 4; // Cambiado de 2 a 4 para la Aspirina más reciente
if ($productModel->delete($deleteId)) {
    echo "Producto eliminado exitosamente. Verifica la lista de productos:<br>";
    $productsAfterDelete = $productModel->getAll();
    echo "<pre>";
    print_r($productsAfterDelete);
    echo "</pre>";
} else {
    echo "Error al eliminar el producto o no existe.";
}

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>