<?php
$title = 'Gestión de Proveedores';
ob_start();
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Proveedores</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProveedorModal">
            <i class="fas fa-plus"></i> Nuevo Proveedor
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
                        <input type="text" class="form-control" placeholder="Buscar proveedores..." 
                               onkeyup="filterTable(this, 'proveedoresTable')">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="estadoFilter">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="tipoFilter">
                        <option value="">Todos los tipos</option>
                        <option value="nacional">Nacional</option>
                        <option value="internacional">Internacional</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="printElement('proveedoresTable')">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Proveedores Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="proveedoresTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($proveedores)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay proveedores registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td><?= $proveedor['id_proveedor'] ?? '' ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building me-2"></i>
                                        <?= $proveedor['nombre'] ?? '' ?>
                                    </div>
                                </td>
                                <td><?= $proveedor['contacto'] ?? '' ?></td>
                                <td><?= $proveedor['telefono'] ?? '' ?></td>
                                <td><?= $proveedor['email'] ?? '' ?></td>
                                <td>
                                    <span class="badge bg-<?= ($proveedor['estado'] ?? '') === 'activo' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($proveedor['estado'] ?? '') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="index.php?controller=proveedor&action=edit&id=<?= $proveedor['id_proveedor'] ?? '' ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=proveedor&action=view&id=<?= $proveedor['id_proveedor'] ?? '' ?>" 
                                           class="btn btn-sm btn-outline-info"
                                           data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('¿Estás seguro de que deseas eliminar este proveedor?')) { 
                                                    fetch('index.php?controller=proveedor&action=delete&id=<?= $proveedor['id_proveedor'] ?? '' ?>', {
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
                                                            alert('Error al eliminar el proveedor: ' + data.message);
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Error al eliminar el proveedor');
                                                    });
                                                }"
                                                data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Proveedor Modal -->
<div class="modal fade" id="addProveedorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProveedorForm" action="index.php?controller=proveedor&action=store" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="nacional">Nacional</option>
                                <option value="internacional">Internacional</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Persona de Contacto</label>
                            <input type="text" class="form-control" name="contacto" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addProveedorForm" class="btn btn-primary">Guardar Proveedor</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>