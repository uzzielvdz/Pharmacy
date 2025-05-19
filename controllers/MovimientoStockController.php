<?php
require_once dirname(__DIR__) . '/models/MovimientoStock.php';

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
        $errors = []; // Inicializar errores vacíos
        require_once dirname(__DIR__) . '/views/movimientos/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ['Método no permitido. Usa el formulario para registrar el movimiento.'];
            $productos = $this->movimientoModel->getProductos();
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

        // Mostrar formulario con errores
        $productos = $this->movimientoModel->getProductos();
        require_once dirname(__DIR__) . '/views/movimientos/create.php';
    }
}
?>