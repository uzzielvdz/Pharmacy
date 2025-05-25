<?php
require_once dirname(__DIR__) . '/models/Product.php';
require_once dirname(__DIR__) . '/models/MovimientoStock.php';

class ProductController {
    private $productModel;
    private $movimientoModel;

    public function __construct() {
        try {
            $this->productModel = new Product();
            $this->movimientoModel = new MovimientoStock();
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo inicializar los modelos.");
        }
    }

    public function index() {
        $errors = [];
        $products = [];
        try {
            $products = $this->productModel->getAll();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once dirname(__DIR__) . '/views/products/index.php';
    }

    public function create() {
        $errors = [];
        $proveedores = [];
        $formData = $_POST ?: [];
        try {
            $proveedores = $this->productModel->getProveedores();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once dirname(__DIR__) . '/views/products/create.php';
    }

    public function store() {
        $errors = [];
        $formData = [];
        $proveedores = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Por favor, usa el formulario para registrar el producto.");
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
            $formData = $data;

            if (empty($data['nombre'])) {
                throw new Exception("E006 Validación: El nombre del producto es obligatorio.");
            }
            if ($data['stock'] === false || $data['stock'] < 0) {
                throw new Exception("E006 Validación: El stock debe ser un número no negativo.");
            }
            if ($data['precio'] === false || $data['precio'] <= 0) {
                throw new Exception("E006 Validación: El precio debe ser un número positivo.");
            }
            if ($data['id_proveedor'] === false || $data['id_proveedor'] <= 0) {
                throw new Exception("E006 Validación: Por favor, selecciona un proveedor válido.");
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha_caducidad'])) {
                throw new Exception("E006 Validación: La fecha de caducidad no es válida.");
            }

            $id_producto = $this->productModel->create($data);
            if ($id_producto && $data['stock'] > 0) {
                $movimientoData = [
                    'id_producto' => $id_producto,
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $data['stock'],
                    'fecha' => date('Y-m-d H:i:s'),
                    'motivo' => 'Creación de producto #' . $id_producto
                ];
                $this->movimientoModel->create($movimientoData);
            }
            header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            try {
                $proveedores = $this->productModel->getProveedores();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            require_once dirname(__DIR__) . '/views/products/create.php';
        }
    }

    public function edit($id) {
        $errors = [];
        $product = null;
        $proveedores = [];
        $formData = $_POST ?: [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de producto no es válido.");
            }
            $product = $this->productModel->getById($id);
            if (!$product) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
            $proveedores = $this->productModel->getProveedores();
            $formData = $formData ?: $product;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
            return;
        }
        require_once dirname(__DIR__) . '/views/products/edit.php';
    }

    public function update() {
        $errors = [];
        $formData = [];
        $proveedores = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Por favor, usa el formulario para actualizar el producto.");
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
            $formData = $data;
            $formData['id_producto'] = $id;

            if ($id === false || $id <= 0) {
                throw new Exception("E006 Validación: El ID de producto no es válido.");
            }
            if (empty($data['nombre'])) {
                throw new Exception("E006 Validación: El nombre del producto es obligatorio.");
            }
            if ($data['stock'] === false || $data['stock'] < 0) {
                throw new Exception("E006 Validación: El stock debe ser un número no negativo.");
            }
            if ($data['precio'] === false || $data['precio'] <= 0) {
                throw new Exception("E006 Validación: El precio debe ser un número positivo.");
            }
            if ($data['id_proveedor'] === false || $data['id_proveedor'] <= 0) {
                throw new Exception("E006 Validación: Por favor, selecciona un proveedor válido.");
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha_caducidad'])) {
                throw new Exception("E006 Validación: La fecha de caducidad no es válida.");
            }

            $currentProduct = $this->productModel->getById($id);
            if (!$currentProduct) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
            $stockDifference = $data['stock'] - $currentProduct['stock'];
            $this->productModel->update($id, $data);
            if ($stockDifference != 0) {
                $movimientoData = [
                    'id_producto' => $id,
                    'tipo_movimiento' => $stockDifference > 0 ? 'entrada' : 'salida',
                    'cantidad' => abs($stockDifference),
                    'fecha' => date('Y-m-d H:i:s'),
                    'motivo' => 'Ajuste por edición #' . $id
                ];
                $this->movimientoModel->create($movimientoData);
            }
            header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            try {
                $proveedores = $this->productModel->getProveedores();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            require_once dirname(__DIR__) . '/views/products/edit.php';
        }
    }

    public function delete($id) {
        $errors = [];
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de producto no es válido.");
            }
            $product = $this->productModel->getById($id);
            if (!$product) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
            if ($product['stock'] > 0) {
                $movimientoData = [
                    'id_producto' => $id,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $product['stock'],
                    'fecha' => date('Y-m-d H:i:s'),
                    'motivo' => 'Eliminación de producto #' . $id
                ];
                $this->movimientoModel->create($movimientoData);
            }
            $this->productModel->delete($id);
            header('Location: ' . BASE_URL . '/public/index.php?controller=product&action=index');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $products = $this->productModel->getAll();
            require_once dirname(__DIR__) . '/views/products/index.php';
        }
    }
}
?>