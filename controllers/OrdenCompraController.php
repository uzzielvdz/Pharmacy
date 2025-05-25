<?php
require_once dirname(__DIR__) . '/models/OrdenCompra.php';

class OrdenCompraController {
    private $ordenModel;

    public function __construct() {
        $this->ordenModel = new OrdenCompra();
    }

    public function index() {
        $errors = [];
        $ordenes = [];
        try {
            $ordenes = $this->ordenModel->getAll();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once dirname(__DIR__) . '/views/ordenes/index.php';
    }

    public function create() {
        $errors = [];
        $proveedores = [];
        $productos = [];
        try {
            $proveedores = $this->ordenModel->getProveedores();
            $productos = $this->ordenModel->getProductos();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
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

            if (!$data['id_proveedor'] || $data['id_proveedor'] <= 0) {
                throw new Exception("E006 Validación: El proveedor seleccionado no es válido.");
            }
            if (!$data['fecha_orden'] || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data['fecha_orden'])) {
                throw new Exception("E006 Validación: La fecha de la orden no es válida.");
            }

            $productos = $_POST['productos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $precios = $_POST['precios'] ?? [];

            for ($i = 0; $i < count($productos); $i++) {
                if (!empty($productos[$i]) && !empty($cantidades[$i]) && !empty($precios[$i])) {
                    $producto_id = filter_var($productos[$i], FILTER_VALIDATE_INT);
                    $cantidad = filter_var($cantidades[$i], FILTER_VALIDATE_INT);
                    $precio = filter_var($precios[$i], FILTER_VALIDATE_FLOAT);
                    if ($producto_id <= 0 || $cantidad <= 0 || $precio <= 0) {
                        throw new Exception("E006 Validación: Los detalles de la orden contienen valores no válidos.");
                    }
                    $data['detalles'][] = [
                        'id_producto' => $producto_id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio
                    ];
                    $data['total'] += $cantidad * $precio;
                }
            }

            if (empty($data['detalles'])) {
                throw new Exception("E006 Validación: Debe añadir al menos un producto a la orden.");
            }

            $this->ordenModel->create($data);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $proveedores = $this->ordenModel->getProveedores();
            $productos = $this->ordenModel->getProductos();
            require_once dirname(__DIR__) . '/views/ordenes/create.php';
        }
    }

    public function completar($id) {
        $errors = [];
        try {
            if ($id <= 0) {
                throw new Exception("E006 Validación: El ID de la orden no es válido.");
            }
            $this->ordenModel->completar($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }

    public function cancelar($id) {
        $errors = [];
        try {
            if ($id <= 0) {
                throw new Exception("E006 Validación: El ID de la orden no es válido.");
            }
            $this->ordenModel->cancelar($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }

    public function delete($id) {
        $errors = [];
        try {
            if ($id <= 0) {
                throw new Exception("E006 Validación: El ID de la orden no es válido.");
            }
            $this->ordenModel->delete($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=orden&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $ordenes = $this->ordenModel->getAll();
            require_once dirname(__DIR__) . '/views/ordenes/index.php';
        }
    }
}
?>