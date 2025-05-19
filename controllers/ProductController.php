<?php
require_once dirname(__DIR__) . '/models/Product.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        $products = $this->productModel->getAll();
        require_once dirname(__DIR__) . '/views/products/index.php';
    }

    public function create() {
        $proveedores = $this->productModel->getProveedores();
        require_once dirname(__DIR__) . '/views/products/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Método no permitido.";
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio' => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'fecha_caducidad' => trim($_POST['fecha_caducidad'] ?? ''),
            'lote' => trim($_POST['lote'] ?? ''),
            'stock' => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'id_proveedor' => filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT)
        ];

        if (empty($data['nombre']) || $data['precio'] === false || empty($data['fecha_caducidad']) || 
            empty($data['lote']) || $data['stock'] === false || $data['id_proveedor'] === false) {
            echo "Error: Todos los campos obligatorios deben ser válidos.";
            return;
        }

        try {
            if ($this->productModel->create($data)) {
                header('Location: ' . BASE_URL . '/public/index.php');
                exit;
            } else {
                echo "Error al crear el producto. Verifica los datos.";
            }
        } catch (Exception $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function edit($id) {
        $product = $this->productModel->getById($id);
        $proveedores = $this->productModel->getProveedores();
        if ($product) {
            require_once dirname(__DIR__) . '/views/products/edit.php';
        } else {
            echo "Producto no encontrado.";
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
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio' => filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT),
            'fecha_caducidad' => trim($_POST['fecha_caducidad'] ?? ''),
            'lote' => trim($_POST['lote'] ?? ''),
            'stock' => filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT),
            'id_proveedor' => filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT)
        ];

        if (empty($data['nombre']) || $data['precio'] === false || empty($data['fecha_caducidad']) || 
            empty($data['lote']) || $data['stock'] === false || $data['id_proveedor'] === false) {
            echo "Error: Todos los campos obligatorios deben ser válidos.";
            return;
        }

        try {
            if ($this->productModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/public/index.php');
                exit;
            } else {
                echo "Error al actualizar el producto. Verifica los datos.";
            }
        } catch (Exception $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            if ($this->productModel->delete($id)) {
                header('Location: ' . BASE_URL . '/public/index.php');
                exit;
            } else {
                echo "Error al eliminar el producto.";
            }
        } catch (Exception $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>