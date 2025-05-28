<?php
require_once dirname(__DIR__) . '/models/ProveedorModel.php';

class ProveedorController {
    private $proveedorModel;

    public function __construct() {
        try {
            $this->proveedorModel = new ProveedorModel();
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
        require_once VIEWS_PATH . '/proveedores/index.php';
    }

    public function create() {
        $errors = [];
        $formData = $_POST ?: [];
        require_once VIEWS_PATH . '/proveedores/create.php';
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
                'tipo' => trim($_POST['tipo'] ?? ''),
                'contacto' => trim($_POST['contacto'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'estado' => trim($_POST['estado'] ?? 'activo'),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'notas' => trim($_POST['notas'] ?? '')
            ];
            $formData = $data;

            if (empty($data['nombre']) || empty($data['tipo']) || empty($data['contacto']) || 
                empty($data['telefono']) || empty($data['email']) || empty($data['direccion'])) {
                throw new Exception("E006 Validación: Todos los campos son obligatorios.");
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E006 Validación: El email no es válido.");
            }
            if (!in_array($data['tipo'], ['nacional', 'internacional'])) {
                throw new Exception("E006 Validación: El tipo de proveedor no es válido.");
            }
            if (!in_array($data['estado'], ['activo', 'inactivo'])) {
                throw new Exception("E006 Validación: El estado no es válido.");
            }

            $this->proveedorModel->create($data);
            setFlash('Proveedor creado exitosamente', 'success');
            redirect('proveedor');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            require_once VIEWS_PATH . '/proveedores/create.php';
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
            require_once VIEWS_PATH . '/proveedores/index.php';
            return;
        }
        require_once VIEWS_PATH . '/proveedores/edit.php';
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
                'tipo' => trim($_POST['tipo'] ?? ''),
                'contacto' => trim($_POST['contacto'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'estado' => trim($_POST['estado'] ?? 'activo'),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'notas' => trim($_POST['notas'] ?? '')
            ];
            $formData = $data;
            $formData['id_proveedor'] = $id;

            if (empty($data['nombre']) || empty($data['tipo']) || empty($data['contacto']) || 
                empty($data['telefono']) || empty($data['email']) || empty($data['direccion'])) {
                throw new Exception("E006 Validación: Todos los campos son obligatorios.");
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E006 Validación: El email no es válido.");
            }
            if (!in_array($data['tipo'], ['nacional', 'internacional'])) {
                throw new Exception("E006 Validación: El tipo de proveedor no es válido.");
            }
            if (!in_array($data['estado'], ['activo', 'inactivo'])) {
                throw new Exception("E006 Validación: El estado no es válido.");
            }

            $this->proveedorModel->update($id, $data);
            setFlash('Proveedor actualizado exitosamente', 'success');
            redirect('proveedor');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $proveedor = $formData;
            require_once VIEWS_PATH . '/proveedores/edit.php';
        }
    }

    public function delete($id) {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de proveedor no es válido.");
            }

            // Verificar si el proveedor tiene productos asociados
            $productos = $this->proveedorModel->getProductosByProveedor($id);
            if (!empty($productos)) {
                throw new Exception("No se puede eliminar el proveedor porque tiene productos asociados.");
            }

            // Verificar si el proveedor tiene órdenes de compra asociadas
            $ordenes = $this->proveedorModel->getOrdenesByProveedor($id);
            if (!empty($ordenes)) {
                throw new Exception("No se puede eliminar el proveedor porque tiene órdenes de compra asociadas.");
            }

            if ($this->proveedorModel->delete($id)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
                setFlash('Proveedor eliminado exitosamente', 'success');
            } else {
                throw new Exception("Error al eliminar el proveedor.");
            }
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            $errors[] = $e->getMessage();
            $proveedores = $this->proveedorModel->getAll();
            require_once VIEWS_PATH . '/proveedores/index.php';
            return;
        }
        redirect('proveedor');
    }
}
?>