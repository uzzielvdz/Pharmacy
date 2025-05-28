<?php
require_once dirname(__DIR__) . '/models/MovimientoStock.php';

class MovimientoStockController {
    private $movimientoModel;

    public function __construct() {
        try {
            $this->movimientoModel = new MovimientoStock();
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo inicializar el modelo de movimientos.");
        }
    }

    public function index() {
        $errors = [];
        $movimientos = [];
        try {
            $movimientos = $this->movimientoModel->getAll();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once VIEWS_PATH . '/movimientos/index.php';
    }

    public function create() {
        $errors = [];
        $productos = [];
        $formData = $_POST ?: [];
        try {
            $productos = $this->movimientoModel->getProductos();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once VIEWS_PATH . '/movimientos/create.php';
    }

    public function store() {
        $errors = [];
        $formData = [];
        $productos = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Por favor, usa el formulario para registrar el movimiento.");
            }

            $data = [
                'id_producto' => filter_var($_POST['id_producto'] ?? 0, FILTER_VALIDATE_INT),
                'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? ''),
                'cantidad' => filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT),
                'fecha' => !empty($_POST['fecha']) ? date('Y-m-d H:i:s', strtotime($_POST['fecha'])) : '',
                'motivo' => trim($_POST['motivo'] ?? '')
            ];
            $formData = $data;

            if ($data['id_producto'] === false || $data['id_producto'] <= 0) {
                throw new Exception("E006 Validación: Por favor, selecciona un producto válido.");
            }
            if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'])) {
                throw new Exception("E006 Validación: El tipo de movimiento debe ser 'entrada' o 'salida'.");
            }
            if ($data['cantidad'] === false || $data['cantidad'] <= 0) {
                throw new Exception("E006 Validación: La cantidad debe ser un número positivo.");
            }
            if (empty($data['fecha'])) {
                throw new Exception("E006 Validación: La fecha es obligatoria.");
            }
            if (strlen($data['motivo']) > 100) {
                throw new Exception("E006 Validación: El motivo no puede exceder los 100 caracteres.");
            }

            $this->movimientoModel->create($data);
            setFlash('Movimiento registrado exitosamente', 'success');
            redirect('movimiento');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            try {
                $productos = $this->movimientoModel->getProductos();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            require_once VIEWS_PATH . '/movimientos/create.php';
        }
    }

    public function edit($id) {
        $errors = [];
        $movimiento = null;
        $productos = [];
        $formData = $_POST ?: [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de movimiento no es válido.");
            }
            $movimiento = $this->movimientoModel->getById($id);
            if (!$movimiento) {
                throw new Exception("E003 Movimientos: Movimiento no encontrado.");
            }
            $productos = $this->movimientoModel->getProductos();
            $formData = $formData ?: $movimiento;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $movimientos = $this->movimientoModel->getAll();
            require_once VIEWS_PATH . '/movimientos/index.php';
            return;
        }
        require_once VIEWS_PATH . '/movimientos/edit.php';
    }

    public function update() {
        $errors = [];
        $formData = [];
        $productos = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Por favor, usa el formulario para actualizar el movimiento.");
            }

            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
            $data = [
                'id_producto' => filter_var($_POST['id_producto'] ?? 0, FILTER_VALIDATE_INT),
                'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? ''),
                'cantidad' => filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT),
                'fecha' => !empty($_POST['fecha']) ? date('Y-m-d H:i:s', strtotime($_POST['fecha'])) : '',
                'motivo' => trim($_POST['motivo'] ?? '')
            ];
            $formData = $data;

            if ($id === false || $id <= 0) {
                throw new Exception("E006 Validación: El ID de movimiento no es válido.");
            }
            if ($data['id_producto'] === false || $data['id_producto'] <= 0) {
                throw new Exception("E006 Validación: Por favor, selecciona un producto válido.");
            }
            if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'])) {
                throw new Exception("E006 Validación: El tipo de movimiento debe ser 'entrada' o 'salida'.");
            }
            if ($data['cantidad'] === false || $data['cantidad'] <= 0) {
                throw new Exception("E006 Validación: La cantidad debe ser un número positivo.");
            }
            if (empty($data['fecha'])) {
                throw new Exception("E006 Validación: La fecha es obligatoria.");
            }
            if (strlen($data['motivo']) > 100) {
                throw new Exception("E006 Validación: El motivo no puede exceder los 100 caracteres.");
            }

            $this->movimientoModel->update($id, $data);
            setFlash('Movimiento actualizado exitosamente', 'success');
            redirect('movimiento');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            try {
                $productos = $this->movimientoModel->getProductos();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            require_once VIEWS_PATH . '/movimientos/edit.php';
        }
    }

    public function delete($id) {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de movimiento no es válido.");
            }

            // Verificar si el movimiento existe
            $movimiento = $this->movimientoModel->getById($id);
            if (!$movimiento) {
                throw new Exception("E003 Movimientos: Movimiento no encontrado.");
            }

            // Verificar si el movimiento está asociado a una orden de compra
            if (strpos($movimiento['motivo'], 'Orden de compra #') === 0) {
                throw new Exception("E003 Movimientos: No se puede eliminar un movimiento asociado a una orden de compra.");
            }

            // Eliminar el movimiento
            if ($this->movimientoModel->delete($id)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Movimiento eliminado exitosamente']);
                    exit;
                }
                setFlash('Movimiento eliminado exitosamente', 'success');
            } else {
                throw new Exception("Error al eliminar el movimiento.");
            }
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            setFlash($e->getMessage(), 'danger');
        }
        redirect('movimiento');
    }
}
?>