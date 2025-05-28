<?php
$title = 'Gestión de Órdenes de Compra';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Órdenes de Compra</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrdenModal">
            <i class="fas fa-plus"></i> Nueva Orden
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
                        <input type="text" class="form-control" placeholder="Buscar órdenes..." 
                               onkeyup="filterTable(this, 'ordenesTable')">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="estadoFilter">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="proveedorFilter">
                        <option value="">Todos los proveedores</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id_proveedor'] ?>">
                                <?= $proveedor['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="printElement('ordenesTable')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Órdenes Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="ordenesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><?= $orden['id_orden'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-truck me-2"></i>
                                    <?= $orden['nombre_proveedor'] ?>
                                </div>
                            </td>
                            <td><?= date('d/m/Y', strtotime($orden['fecha'])) ?></td>
                            <td>$<?= number_format($orden['total'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $orden['estado'] === 'completada' ? 'success' : 
                                    ($orden['estado'] === 'pendiente' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($orden['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($orden['estado'] === 'pendiente'): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success"
                                            onclick="completarOrden(<?= $orden['id_orden'] ?>)"
                                            data-bs-toggle="tooltip" title="Completar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="cancelarOrden(<?= $orden['id_orden'] ?>)"
                                            data-bs-toggle="tooltip" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php endif; ?>
                                    <a href="index.php?controller=orden&action=view&id=<?= $orden['id_orden'] ?>" 
                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="tooltip" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($orden['estado'] === 'pendiente'): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="if(confirm('¿Estás seguro de que deseas eliminar esta orden?')) { 
                                                fetch('index.php?controller=orden&action=delete&id=<?= $orden['id_orden'] ?>', {
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
                                                        alert('Error al eliminar la orden: ' + data.message);
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Error al eliminar la orden');
                                                });
                                            }"
                                            data-bs-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
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

<!-- Add Orden Modal -->
<div class="modal fade" id="addOrdenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Orden de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addOrdenForm" action="index.php?controller=orden&action=store" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Entrega</label>
                            <input type="date" class="form-control" name="fecha_entrega" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Productos</label>
                        <div id="productosContainer">
                            <div class="row mb-2 producto-row">
                                <div class="col-md-5">
                                    <select class="form-select" name="productos[]" required>
                                        <option value="">Seleccionar producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id_producto'] ?>">
                                                <?= $producto['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="cantidades[]" 
                                           placeholder="Cantidad" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="precios[]" 
                                           placeholder="Precio" step="0.01" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="removeProductoRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" 
                                onclick="addProductoRow()">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addOrdenForm" class="btn btn-primary">Crear Orden</button>
            </div>
        </div>
    </div>
</div>

<script>
function addProductoRow() {
    const container = document.getElementById('productosContainer');
    const newRow = container.querySelector('.producto-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelector('select').selectedIndex = 0;
    container.appendChild(newRow);
}

function removeProductoRow(button) {
    const container = document.getElementById('productosContainer');
    if (container.querySelectorAll('.producto-row').length > 1) {
        button.closest('.producto-row').remove();
    }
}

function completarOrden(id) {
    if (confirm('¿Estás seguro de que deseas marcar esta orden como completada?')) {
        fetch(`index.php?controller=orden&action=completar&id=${id}`, {
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
                alert('Error al completar la orden: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al completar la orden');
        });
    }
}

function cancelarOrden(id) {
    if (confirm('¿Estás seguro de que deseas cancelar esta orden?')) {
        fetch(`index.php?controller=orden&action=cancelar&id=${id}`, {
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
                alert('Error al cancelar la orden: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la orden');
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>