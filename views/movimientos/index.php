<?php
$title = 'Gestión de Movimientos de Stock';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Movimientos de Stock</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMovimientoModal">
            <i class="fas fa-plus"></i> Nuevo Movimiento
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
                        <input type="text" class="form-control" placeholder="Buscar movimientos..." 
                               onkeyup="filterTable(this, 'movimientosTable')">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="tipoFilter">
                        <option value="">Todos los tipos</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                        <option value="ajuste">Ajuste</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="productoFilter">
                        <option value="">Todos los productos</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto['id_producto'] ?>">
                                <?= $producto['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="printElement('movimientosTable')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="movimientosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos)): ?>
                            <?php foreach ($movimientos as $movimiento): ?>
                                <tr>
                                    <td><?= $movimiento['id_movimiento'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= asset('img/products/' . ($movimiento['imagen'] ?? 'default.jpg')) ?>" 
                                                 alt="<?= $movimiento['nombre_producto'] ?>" 
                                                 class="rounded-circle me-2" width="32" height="32">
                                            <?= $movimiento['nombre_producto'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $movimiento['tipo'] === 'entrada' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($movimiento['tipo']) ?>
                                        </span>
                                    </td>
                                    <td><?= $movimiento['cantidad'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha'])) ?></td>
                                    <td><?= $movimiento['motivo'] ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= url('movimiento/edit/' . $movimiento['id_movimiento']) ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-movimiento" 
                                                    data-id="<?= $movimiento['id_movimiento'] ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteMovimientoModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteMovimientoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este movimiento? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteMovimientoForm" action="" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="<?= generateCSRFToken() ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar el formulario de eliminación
            const deleteButtons = document.querySelectorAll('.delete-movimiento');
            const deleteForm = document.getElementById('deleteMovimientoForm');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    deleteForm.action = `<?= url('movimiento/delete/') ?>${id}`;
                });
            });

            // Manejar la respuesta del formulario
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error al eliminar el movimiento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el movimiento');
                });
            });
        });
    </script>
</div>

<!-- Add Movimiento Modal -->
<div class="modal fade" id="addMovimientoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Movimiento de Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMovimientoForm" action="index.php?controller=movimiento&action=store" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Producto</label>
                            <select class="form-select" name="id_producto" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto['id_producto'] ?>">
                                        <?= $producto['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Movimiento</label>
                            <select class="form-select" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                                <option value="ajuste">Ajuste</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="datetime-local" class="form-control" name="fecha" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea class="form-control" name="motivo" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" class="form-control" name="referencia" 
                               placeholder="Número de orden, factura, etc.">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addMovimientoForm" class="btn btn-primary">Registrar Movimiento</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>