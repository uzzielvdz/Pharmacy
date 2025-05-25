<?php
require_once dirname(__DIR__) . '/models/OrdenCompra.php';

class OrdenCompraController {
    private $ordenModel;

    public function __construct() {
        $this->ordenModel = new OrdenCompra();
    }

    public function index() {
        $errors = [];
        try {
            $ordenes = $this->ordenModel->getAll();
        } catch (Exception $e) {
            $errors[] = "Error al cargar órdenes: " . htmlspecialchars($e->getMessage());
            $ordenes = [];
        }
        require_once dirname(__DIR__) . '/views/ordenes/index.php';
    }

    public function create() {
        $proveedores = $this->ordenModel->getProveedores();
        $productos = $this->ordenModel->getProductos();
        require_once dirname(__DIR__) . '/views/ordenes/create.php';
    }

    public function store() {
        $errors = [];
        try {
            $data = [
                'id_proveedor' => filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT),
                'fecha_orden' => filter_input(INPUT_POST, 'fecha_orden', FILTER_SANITIZE_STRING),
                'total' => 0,
                'detalles' => []
            ];

            $productos = $_POST['productos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $precios = $_POST['precios'] ?? [];

            for ($i = 0; $i < count($productos); $i++) {
                if (!empty($productos[$i]) && !empty($cantidades[$i]) && !empty($precios[$i])) {
                    $data['detalles'][] = [
                        'id_producto' => filter_var($productos[$i], FILTER_VALIDATE_INT),
                        'cantidad' => filter_var($cantidades[$i], FILTER_VALIDATE_INT),
                        'precio_unitario' => filter_var($precios[$i], FILTER_VALIDATE_FLOAT)
                    ];
                    $data['total'] += $cantidades[$i] * $precios[$i];
                }
            }

            if (!$data['id_proveedor'] || !$data['fecha_orden'] || empty($data['detalles'])) {
                $errors[] = "Todos los campos son obligatorios.";
            } else {
                $this->ordenModel->create($data);
                header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Error: " . htmlspecialchars($e->getMessage());
        }

        $proveedores = $this->ordenModel->getProveedores();
        $productos = $this->ordenModel->getProductos();
        require_once dirname(__DIR__) . '/views/ordenes/create.php';
    }

    public function completar($id) {
        $errors = [];
        try {
            $this->ordenModel->completar($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = "Error: " . htmlspecialchars($e->getMessage());
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }

    public function cancelar($id) {
        $errors = [];
        try {
            $this->ordenModel->cancelar($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = "Error: " . htmlspecialchars($e->getMessage());
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }

    public function delete($id) {
        $errors = [];
        try {
            $this->ordenModel->delete($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = "Error: " . htmlspecialchars($e->getMessage());
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }
}
?>