<?php
$title = 'Editar Producto';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Producto</h1>
        <a href="<?= url('products') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Edit Product Form -->
    <div class="card">
        <div class="card-body">
            <form action="<?= url('product/update') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $product['id_producto'] ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" name="nombre" value="<?= $product['nombre'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="medicamentos" <?= $product['categoria'] === 'medicamentos' ? 'selected' : '' ?>>Medicamentos</option>
                            <option value="higiene" <?= $product['categoria'] === 'higiene' ? 'selected' : '' ?>>Higiene</option>
                            <option value="cosmeticos" <?= $product['categoria'] === 'cosmeticos' ? 'selected' : '' ?>>Cosméticos</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="precio" step="0.01" value="<?= $product['precio'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" value="<?= $product['stock'] ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Caducidad</label>
                        <input type="date" class="form-control" name="fecha_caducidad" value="<?= $product['fecha_caducidad'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lote</label>
                        <input type="text" class="form-control" name="lote" value="<?= $product['lote'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proveedor</label>
                    <select class="form-select" name="id_proveedor" required>
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id_proveedor'] ?>" <?= $product['id_proveedor'] == $proveedor['id_proveedor'] ? 'selected' : '' ?>>
                                <?= $proveedor['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" rows="3"><?= $product['descripcion'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen del Producto</label>
                    <?php if ($product['imagen']): ?>
                        <div class="mb-2">
                            <img src="<?= asset('img/products/' . $product['imagen']) ?>" alt="Imagen actual" class="img-thumbnail" width="100">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="imagen" accept="image/*">
                    <small class="text-muted">Deja vacío para mantener la imagen actual</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>