<?php
$title = 'Gestión de Productos';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Productos</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> Nuevo Producto
        </button>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Buscar productos..." 
                               onkeyup="filterTable(this, 'productsTable')">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Todas las categorías</option>
                        <option value="medicamentos">Medicamentos</option>
                        <option value="higiene">Higiene</option>
                        <option value="cosmeticos">Cosméticos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="stockFilter">
                        <option value="">Todos los estados</option>
                        <option value="stock">En stock</option>
                        <option value="bajo">Stock bajo</option>
                        <option value="agotado">Agotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="printElement('productsTable')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id_producto'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= asset('img/products/' . ($product['imagen'] ?? 'default.jpg')) ?>" 
                                         alt="<?= $product['nombre'] ?>" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <?= $product['nombre'] ?>
                                </div>
                            </td>
                            <td><?= $product['categoria'] ?></td>
                            <td>$<?= number_format($product['precio'], 2) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td>
                                <span class="badge bg-<?= $product['stock'] > 10 ? 'success' : 
                                    ($product['stock'] > 0 ? 'warning' : 'danger') ?>">
                                    <?= $product['stock'] > 10 ? 'En stock' : 
                                        ($product['stock'] > 0 ? 'Stock bajo' : 'Agotado') ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="index.php?controller=product&action=edit&id=<?= $product['id_producto'] ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?controller=product&action=view&id=<?= $product['id_producto'] ?>" 
                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="tooltip" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="if(confirm('¿Estás seguro de que deseas eliminar este producto?')) { 
                                                fetch('index.php?controller=product&action=delete&id=<?= $product['id_producto'] ?>', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    }
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        window.location.reload();
                                                    } else {
                                                        alert('Error al eliminar el producto: ' + data.message);
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Error al eliminar el producto');
                                                });
                                            }"
                                            data-bs-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="index.php?controller=product&action=store" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="medicamentos">Medicamentos</option>
                                <option value="higiene">Higiene</option>
                                <option value="cosmeticos">Cosméticos</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="precio" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Caducidad</label>
                            <input type="date" class="form-control" name="fecha_caducidad">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lote</label>
                            <input type="text" class="form-control" name="lote">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proveedor</label>
                        <select class="form-select" name="id_proveedor" required>
                            <option value="">Seleccionar proveedor</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?= $proveedor['id_proveedor'] ?>">
                                    <?= $proveedor['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" class="form-control" name="imagen" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addProductForm" class="btn btn-primary">Guardar Producto</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>