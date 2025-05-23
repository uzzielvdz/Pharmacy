<?php
if (!class_exists('MovimientoStock')) {
    require_once dirname(__DIR__) . '/models/MovimientoStock.php';
}

class MovimientoStockController {
    private $movimientoModel;

    public function __construct() {
        $this->movimientoModel = new MovimientoStock();
    }

    public function index() {
        $movimientos = $this->movimientoModel->getAll();
        require_once dirname(__DIR__) . '/views/movimientos/index.php';
    }

    public function create() {
        $productos = $this->movimientoModel->getProductos();
        $errors = $_REQUEST['errors'] ?? [];
        $formData = $_REQUEST['formData'] ?? [];
        require_once dirname(__DIR__) . '/views/movimientos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ['Por favor, usa el formulario para registrar el movimiento.'];
            $productos = $this->movimientoModel->getProductos();
            $formData = [];
            require_once dirname(__DIR__) . '/views/movimientos/create.php';
            return;
        }

        $data = [
            'id_producto' => filter_var($_POST['id_producto'] ?? 0, FILTER_VALIDATE_INT),
            'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? ''),
            'cantidad' => filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT),
            'fecha' => trim($_POST['fecha'] ?? ''),
            'motivo' => trim($_POST['motivo'] ?? '')
        ];

        $errors = [];
        if ($data['id_producto'] === false || $data['id_producto'] <= 0) {
            $errors[] = 'Por favor, selecciona un producto válido.';
        }
        if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'])) {
            $errors[] = 'El tipo de movimiento debe ser "Entrada" o "Salida".';
        }
        if ($data['cantidad'] === false || $data['cantidad'] <= 0) {
            $errors[] = 'La cantidad debe ser un número positivo.';
        }
        if (empty($data['fecha'])) {
            $errors[] = 'La fecha es obligatoria.';
        }
        if (strlen($data['motivo']) > 100) {
            $errors[] = 'El motivo no puede exceder los 100 caracteres.';
        }

        if (empty($errors)) {
            try {
                if ($this->movimientoModel->create($data)) {
                    header('Location: ' . BASE_URL . '/public/index.php?controller=movimiento&action=index');
                    exit;
                } else {
                    $errors[] = 'No se pudo registrar el movimiento. Intenta de nuevo.';
                }
            } catch (Exception $e) {
                $errors[] = htmlspecialchars($e->getMessage());
            }
        }

        $productos = $this->movimientoModel->getProductos();
        $formData = $data;
        require_once dirname(__DIR__) . '/views/movimientos/create.php';
    }

    public function edit($id) {
        $errors = [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("ID de movimiento no válido.");
            }
            $movimiento = $this->movimientoModel->getById($id);
            if (!$movimiento) {
                throw new Exception("Movimiento no encontrado.");
            }
            $productos = $this->movimientoModel->getProductos();
            $formData = $_REQUEST['formData'] ?? $movimiento;
            if (!file_exists(dirname(__DIR__) . '/views/movimientos/edit.php')) {
                throw new Exception("El archivo edit.php no se encuentra en views/movimientos/.");
            }
            require_once dirname(__DIR__) . '/views/movimientos/edit.php';
        } catch (Exception $e) {
            $errors[] = htmlspecialchars($e->getMessage());
            $movimientos = $this->movimientoModel->getAll();
            require_once dirname(__DIR__) . '/views/movimientos/index.php';
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ['Por favor, usa el formulario para actualizar el movimiento.'];
            $movimientos = $this->movimientoModel->getAll();
            require_once dirname(__DIR__) . '/views/movimientos/index.php';
            return;
        }

        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        
        // Convertir la fecha del formato datetime-local al formato de la base de datos
        $fecha = !empty($_POST['fecha']) ? date('Y-m-d H:i:s', strtotime($_POST['fecha'])) : '';
        
        $data = [
            'id_producto' => filter_var($_POST['id_producto'] ?? 0, FILTER_VALIDATE_INT),
            'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? ''),
            'cantidad' => filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT),
            'fecha' => $fecha,
            'motivo' => trim($_POST['motivo'] ?? '')
        ];

        $errors = [];
        if ($id === false || $id <= 0) {
            $errors[] = 'ID de movimiento no válido.';
        }
        if ($data['id_producto'] === false || $data['id_producto'] <= 0) {
            $errors[] = 'Por favor, selecciona un producto válido.';
        }
        if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'])) {
            $errors[] = 'El tipo de movimiento debe ser "Entrada" o "Salida".';
        }
        if ($data['cantidad'] === false || $data['cantidad'] <= 0) {
            $errors[] = 'La cantidad debe ser un número positivo.';
        }
        if (empty($data['fecha'])) {
            $errors[] = 'La fecha es obligatoria.';
        }
        if (strlen($data['motivo']) > 100) {
            $errors[] = 'El motivo no puede exceder los 100 caracteres.';
        }

        if (empty($errors)) {
            try {
                if ($this->movimientoModel->update($id, $data)) {
                    header('Location: ' . BASE_URL . '/public/index.php?controller=movimiento&action=index');
                    exit;
                } else {
                    $errors[] = 'No se pudo actualizar el movimiento. Intenta de nuevo.';
                }
            } catch (Exception $e) {
                $errors[] = htmlspecialchars($e->getMessage());
            }
        }

        $productos = $this->movimientoModel->getProductos();
        $formData = $data;
        require_once dirname(__DIR__) . '/views/movimientos/edit.php';
    }

    public function delete($id) {
        $errors = [];
        try {
            if ($this->movimientoModel->delete($id)) {
                header('Location: ' . BASE_URL . '/public/index.php?controller=movimiento&action=index');
                exit;
            } else {
                $errors[] = 'No se pudo eliminar el movimiento. Intenta de nuevo.';
            }
        } catch (Exception $e) {
            $errors[] = htmlspecialchars($e->getMessage());
        }

        $movimientos = $this->movimientoModel->getAll();
        require_once dirname(__DIR__) . '/views/movimientos/index.php';
    }
}
?>