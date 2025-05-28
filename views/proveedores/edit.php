<?php
$title = 'Editar Proveedor';
ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Proveedor</h1>
        <a href="index.php?controller=proveedor&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger animate-slide-in" role="alert">
            <ul class="list-unstyled mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="index.php?controller=proveedor&action=update" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_proveedor" value="<?php echo isset($formData['id_proveedor']) ? htmlspecialchars($formData['id_proveedor']) : ''; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?php echo isset($formData['nombre']) ? htmlspecialchars($formData['nombre']) : ''; ?>" 
                               required>
                        <div class="invalid-feedback">Por favor ingrese el nombre de la empresa</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="nacional" <?php echo (isset($formData['tipo']) && $formData['tipo'] === 'nacional') ? 'selected' : ''; ?>>Nacional</option>
                            <option value="internacional" <?php echo (isset($formData['tipo']) && $formData['tipo'] === 'internacional') ? 'selected' : ''; ?>>Internacional</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el tipo de proveedor</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Persona de Contacto <span class="text-danger">*</span></label>
                        <input type="text" name="contacto" class="form-control" 
                               value="<?php echo isset($formData['contacto']) ? htmlspecialchars($formData['contacto']) : ''; ?>" 
                               required>
                        <div class="invalid-feedback">Por favor ingrese el nombre del contacto</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" name="telefono" class="form-control" 
                               value="<?php echo isset($formData['telefono']) ? htmlspecialchars($formData['telefono']) : ''; ?>" 
                               required>
                        <div class="invalid-feedback">Por favor ingrese el teléfono</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>" 
                               required>
                        <div class="invalid-feedback">Por favor ingrese un email válido</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" class="form-select" required>
                            <option value="activo" <?php echo (!isset($formData['estado']) || $formData['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo (isset($formData['estado']) && $formData['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el estado</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                    <textarea name="direccion" class="form-control" rows="2" required><?php echo isset($formData['direccion']) ? htmlspecialchars($formData['direccion']) : ''; ?></textarea>
                    <div class="invalid-feedback">Por favor ingrese la dirección</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="3"><?php echo isset($formData['notas']) ? htmlspecialchars($formData['notas']) : ''; ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php?controller=proveedor&action=index" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validación del formulario
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/base.php';
?>