<?php
require_once dirname(__DIR__) . '/models/Product.php';
require_once dirname(__DIR__) . '/models/MovimientoStock.php';

class ProductController {
    private $productModel;
    private $movimientoModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->movimientoModel = new MovimientoStock();
    }

    public function index() {
        $products = $this->productModel->getAll();
        $errors = $_REQUEST['errors'] ?? [];
        require_once dirname(__DIR__) . '/views/products/index.php';
    }

    public function create() {
        $proveedores = $this->productModel->getProveedores();
        $errors = $_REQUEST['errors'] ?? [];
        $formData = $_REQUEST['formData'] ?? [];
        require_once dirname(__DIR__) . '/views/products/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ['Por favor, usa el formulario para registrar el producto.'];
            $proveedores = $this->productModel->getProveedores();
            $formData = [];
            require_once dirname(__DIR__) . '/views/products/create.php';
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'stock' => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'precio' => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'fecha_caducidad' => trim($_POST['fecha_caducidad'] ?? ''),
            'lote' => trim($_POST['lote'] ?? ''),
            'id_proveedor' => filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT)
        ];

        $errors = [];
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre del producto es obligatorio.';
        }
        if ($data['stock'] === false || $data['stock'] < 0) {
            $errors[] = 'El stock debe ser un número no negativo.';
        }
        if ($data['precio'] === false || $data['precio'] <= 0) {
            $errors[] = 'El precio debe ser un número positivo.';
        }
        if ($data['id_proveedor'] === false || $data['id_proveedor'] <= 0) {
            $errors[] = 'Por favor, selecciona un proveedor válido.';
        }

        if (empty($errors)) {
            try {
                $id_producto = $this->productModel->create($data);
                if ($id_producto && $data['stock'] > 0) {
                    $movimientoData = [
                        'id_producto' => $id_producto,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $data['stock'],
                        'fecha' => date('Y-m-d H:i:s'),
                        'motivo' => 'Creación de producto'
                    ];
                    $this->movimientoModel->create($movimientoData);
                }
                header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
                exit;
            } catch (Exception $e) {
                $errors[] = htmlspecialchars($e->getMessage());
            }
        }

        $proveedores = $this->productModel->getProveedores();
        $formData = $data;
        require_once dirname(__DIR__) . '/views/products/create.php';
    }

    public function edit($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            $errors = ['ID de producto no válido.'];
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
            return;
        }
        $product = $this->productModel->getById($id);
        $proveedores = $this->productModel->getProveedores();
        if (!$product) {
            $errors = ['Producto no encontrado.'];
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
            return;
        }
        $formData = $_REQUEST['formData'] ?? $product;
        require_once dirname(__DIR__) . '/views/products/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ['Por favor, usa el formulario para actualizar el producto.'];
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
            return;
        }

        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'stock' => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'precio' => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'fecha_caducidad' => trim($_POST['fecha_caducidad'] ?? ''),
            'lote' => trim($_POST['lote'] ?? ''),
            'id_proveedor' => filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT)
        ];

        $errors = [];
        if ($id === false || $id <= 0) {
            $errors[] = 'ID de producto no válido.';
        }
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre del producto es obligatorio.';
        }
        if ($data['stock'] === false || $data['stock'] < 0) {
            $errors[] = 'El stock debe ser un número no negativo.';
        }
        if ($data['precio'] === false || $data['precio'] <= 0) {
            $errors[] = 'El precio debe ser un número positivo.';
        }
        if ($data['id_proveedor'] === false || $data['id_proveedor'] <= 0) {
            $errors[] = 'Por favor, selecciona un proveedor válido.';
        }

        if (empty($errors)) {
            try {
                $currentProduct = $this->productModel->getById($id);
                if (!$currentProduct) {
                    $errors[] = 'Producto no encontrado.';
                } else {
                    $stockDifference = $data['stock'] - $currentProduct['stock'];
                    $this->productModel->update($id, $data);
                    if ($stockDifference != 0) {
                        $movimientoData = [
                            'id_producto' => $id,
                            'tipo_movimiento' => $stockDifference > 0 ? 'entrada' : 'salida',
                            'cantidad' => abs($stockDifference),
                            'fecha' => date('Y-m-d H:i:s'),
                            'motivo' => 'Ajuste por edición'
                        ];
                        $this->movimientoModel->create($movimientoData);
                    }
                    header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
                    exit;
                }
            } catch (Exception $e) {
                $errors[] = htmlspecialchars($e->getMessage());
            }
        }

        $formData = $data;
        $formData['id_producto'] = $id;
        $proveedores = $this->productModel->getProveedores();
        require_once dirname(__DIR__) . '/views/products/edit.php';
    }

    public function delete($id) {
        $errors = [];
        try {
            $product = $this->productModel->getById($id);
            if (!$product) {
                $errors[] = 'Producto no encontrado.';
            } else {
                if ($product['stock'] > 0) {
                    $movimientoData = [
                        'id_producto' => $id,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $product['stock'],
                        'fecha' => date('Y-m-d H:i:s'),
                        'motivo' => 'Eliminación de producto'
                    ];
                    $this->movimientoModel->create($movimientoData);
                }
                $this->productModel->delete($id);
                header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = htmlspecialchars($e->getMessage());
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
        }
    }
}
?>