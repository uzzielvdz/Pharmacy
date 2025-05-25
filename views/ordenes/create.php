<?php include dirname(__DIR__) . '/layouts/header.php'; ?>
<div class="card bg-gray-700 animate-slide-in">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Crear Orden de Compra</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate-slide-in" role="alert">
                <ul class="list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=store" method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Proveedor</label>
                <select name="id_proveedor" class="form-select" required>
                    <option value="">Seleccione un proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo $proveedor['id_proveedor']; ?>" <?php echo (isset($formData['id_proveedor']) && $formData['id_proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($proveedor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="datetime-local" name="fecha_orden" class="form-control" value="<?php echo isset($formData['fecha_orden']) ? date('Y-m-d\TH:i', strtotime($formData['fecha_orden'])) : ''; ?>" required>
            </div>
            <div class="col-12">
                <h3 class="text-xl mt-4 mb-2">Detalles de la Orden</h3>
                <div id="detalles">
                    <div class="detalle row g-3 mb-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Producto</label>
                            <select name="productos[]" class="form-select" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?php echo $producto['id_producto']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidades[]" min="1" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Precio Unitario</label>
                            <input type="number" name="precios[]" step="0.01" min="0" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-detail">Eliminar</button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="agregarDetalle()" class="btn btn-primary">Agregar Producto</button>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Crear Orden</button>
                <a href="<?php echo BASE_URL; ?>/public/index.php?controller=orden&action=index" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
function agregarDetalle() {
    const detalles = document.getElementById('detalles');
    const nuevoDetalle = detalles.firstElementChild.cloneNode(true);
    nuevoDetalle.querySelector('select').value = '';
    nuevoDetalle.querySelectorAll('input').forEach(input => input.value = '');
    nuevoDetalle.classList.add('animate-slide-in');
    detalles.appendChild(nuevoDetalle);
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const detalles = document.querySelectorAll('#detalles .detalle');
    detalles.forEach((detalle, index) => {
        const removeBtn = detalle.querySelector('.remove-detail');
        if (detalles.length === 1) {
            removeBtn.style.display = 'none';
        } else {
            removeBtn.style.display = 'block';
            removeBtn.onclick = () => {
                detalle.remove();
                updateRemoveButtons();
            };
        }
    });
}

document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>
<?php include dirname(__DIR__) . '/layouts/footer.php'; ?>