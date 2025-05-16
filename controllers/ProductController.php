<?php
require_once '../models/Product.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        $products = $this->productModel->getAll();
        require '../views/products/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                'stock' => filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT),
                'precio' => filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'fecha_caducidad' => filter_input(INPUT_POST, 'fecha_caducidad', FILTER_SANITIZE_STRING),
                'id_proveedor' => filter_input(INPUT_POST, 'id_proveedor', FILTER_SANITIZE_NUMBER_INT)
            ];
            if ($this->productModel->create($data)) {
                header('Location: ' . BASE_URL . '/public/');
                exit;
            } else {
                $error = "Error al crear el producto.";
            }
        }
        $proveedores = $this->getProveedores();
        require '../views/products/create.php';
    }

    public function edit($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: ' . BASE_URL . '/public/');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING),
                'stock' => filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT),
                'precio' => filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'fecha_caducidad' => filter_input(INPUT_POST, 'fecha_caducidad', FILTER_SANITIZE_STRING),
                'id_proveedor' => filter_input(INPUT_POST, 'id_proveedor', FILTER_SANITIZE_NUMBER_INT)
            ];
            if ($this->productModel->update($id, $data)) {
                header('Location: ' . BASE_URL . '/public/');
                exit;
            } else {
                $error = "Error al actualizar el producto.";
            }
        }
        $product = $this->productModel->getById($id);
        $proveedores = $this->getProveedores();
        require '../views/products/edit.php';
    }

    public function delete($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id && $this->productModel->delete($id)) {
            header('Location: ' . BASE_URL . '/public/');
            exit;
        } else {
            $error = "Error al eliminar el producto.";
            require '../views/products/index.php';
        }
    }

    private function getProveedores() {
        $sql = "SELECT id_proveedor, nombre FROM Proveedores";
        $result = $this->productModel->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>