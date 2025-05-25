<?php
require_once dirname(__DIR__) . '/models/Proveedor.php';

class ProveedorController {
    private $proveedorModel;

    public function __construct() {
        try {
            $this->proveedorModel = new Proveedor();
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo inicializar el modelo de proveedores.");
        }
    }

    public function index() {
        $errors = [];
        $proveedores = [];
        try {
            $proveedores = $this->proveedorModel->getAll();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once dirname(__DIR__) . '/views/proveedores/index.php';
    }

    public function create() {
        $errors = [];
        $formData = $_POST ?: [];
        require_once dirname(__DIR__) . '/views/proveedores/create.php';
    }

    public function store() {
        $errors = [];
        $formData = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Método no permitido.");
            }

            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'contacto' => trim($_POST['contacto'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];
            $formData = $data;

            if (empty($data['nombre']) || empty($data['contacto']) || empty($data['email'])) {
                throw new Exception("E006 Validación: Todos los campos son obligatorios.");
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E006 Validación: El email no es válido.");
            }

            $this->proveedorModel->create($data);
            header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            require_once dirname(__DIR__) . '/views/proveedores/create.php';
        }
    }

    public function edit($id) {
        $errors = [];
        $proveedor = null;
        $formData = $_POST ?: [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de proveedor no es válido.");
            }
            $proveedor = $this->proveedorModel->getById($id);
            if (!$proveedor) {
                throw new Exception("E002 Proveedores: Proveedor no encontrado.");
            }
            $formData = $formData ?: $proveedor;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $proveedores = $this->proveedorModel->getAll();
            require_once dirname(__DIR__) . '/views/proveedores/index.php';
            return;
        }
        require_once dirname(__DIR__) . '/views/proveedores/edit.php';
    }

    public function update() {
        $errors = [];
        $formData = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Método no permitido.");
            }
            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                throw new Exception("E006 Validación: El ID de proveedor no es válido.");
            }

            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'contacto' => trim($_POST['contacto'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];
            $formData = $data;
            $formData['id_proveedor'] = $id;

            if (empty($data['nombre']) || empty($data['contacto']) || empty($data['email'])) {
                throw new Exception("E006 Validación: Todos los campos son obligatorios.");
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E006 Validación: El email no es válido.");
            }

            $this->proveedorModel->update($id, $data);
            header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $proveedor = $formData;
            require_once dirname(__DIR__) . '/views/proveedores/edit.php';
        }
    }

    public function delete($id) {
        $errors = [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de proveedor no es válido.");
            }
            $this->proveedorModel->delete($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $proveedores = $this->proveedorModel->getAll();
            require_once dirname(__DIR__) . '/views/proveedores/index.php';
        }
    }
}
?>