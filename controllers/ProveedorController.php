<?php
require_once dirname(__DIR__) . '/models/Proveedor.php';

class ProveedorController {
    private $proveedorModel;

    public function __construct() {
        $this->proveedorModel = new Proveedor();
    }

    public function index() {
        $proveedores = $this->proveedorModel->getAll();
        require_once dirname(__DIR__) . '/views/proveedores/index.php';
    }

    public function create() {
        
        if (file_exists(dirname(__DIR__) . '/views/proveedores/create.php')) {
            require_once dirname(__DIR__) . '/views/proveedores/create.php';
        } else {
            echo "<br>Archivo NO existe";
        }
        exit;
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Método no permitido.";
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'contacto' => trim($_POST['contacto'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        if (empty($data['nombre']) || empty($data['contacto']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo "Error: Todos los campos son obligatorios y el email debe ser válido.";
            return;
        }

        try {
            if ($this->proveedorModel->create($data)) {
                header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
                exit;
            } else {
                echo "Error al crear el proveedor.";
            }
        } catch (Exception $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function edit($id) {
        $proveedor = $this->proveedorModel->getById($id);
        if ($proveedor) {
            require_once dirname(__DIR__) . '/views/proveedores/edit.php';
        } else {
            echo "Proveedor no encontrado.";
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            echo "Método no permitido o ID no proporcionado.";
            return;
        }

        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            echo "Error: ID inválido.";
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'contacto' => trim($_POST['contacto'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        if (empty($data['nombre']) || empty($data['contacto']) || empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo "Error: Todos los campos son obligatorios y el email debe ser válido.";
            return;
        }

        try {
            if ($this->proveedorModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
                exit;
            } else {
                echo "Error al actualizar el proveedor.";
            }
        } catch (Exception $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            if ($this->proveedorModel->delete($id)) {
                header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index');
                exit;
            } else {
                header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index&error=' . urlencode('Error al eliminar el proveedor.'));
                exit;
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '/public/index.php?controller=proveedor&action=index&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>