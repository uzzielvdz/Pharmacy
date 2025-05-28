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
        $proveedores = [];
        try {
            $products = $this->productModel->getAll();
            $proveedores = $this->productModel->getProveedores();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        require_once VIEWS_PATH . '/products/index.php';
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
        require_once VIEWS_PATH . '/products/create.php';
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
                'id_proveedor' => filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT),
                'categoria' => trim($_POST['categoria'] ?? 'medicamentos')
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

            // Manejar la imagen si se subió una
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/img/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $newFileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadFile)) {
                    $data['imagen'] = $newFileName;
                }
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
            setFlash('Producto creado exitosamente', 'success');
            redirect('products');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            try {
                $proveedores = $this->productModel->getProveedores();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            require_once VIEWS_PATH . '/products/create.php';
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
            require_once VIEWS_PATH . '/products/index.php';
            return;
        }
        require_once VIEWS_PATH . '/products/edit.php';
    }

    public function update() {
        $errors = [];
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("E006 Validación: Por favor, usa el formulario para actualizar el producto.");
            }

            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception("E006 Validación: ID de producto no válido.");
            }

            // Validar datos
            if (empty($_POST['nombre'])) {
                throw new Exception("E006 Validación: El nombre del producto es obligatorio.");
            }
            if (!filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT) || $_POST['precio'] <= 0) {
                throw new Exception("E006 Validación: El precio debe ser un número positivo.");
            }
            if (!filter_var($_POST['stock'], FILTER_VALIDATE_INT) || $_POST['stock'] < 0) {
                throw new Exception("E006 Validación: El stock debe ser un número no negativo.");
            }
            if (!filter_var($_POST['id_proveedor'], FILTER_VALIDATE_INT) || $_POST['id_proveedor'] <= 0) {
                throw new Exception("E006 Validación: Por favor, selecciona un proveedor válido.");
            }

            $data = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'precio' => filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT),
                'fecha_caducidad' => trim($_POST['fecha_caducidad'] ?? ''),
                'lote' => trim($_POST['lote'] ?? ''),
                'stock' => filter_var($_POST['stock'], FILTER_VALIDATE_INT),
                'categoria' => trim($_POST['categoria'] ?? 'medicamentos'),
                'id_proveedor' => filter_var($_POST['id_proveedor'], FILTER_VALIDATE_INT)
            ];

            // Manejar la imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/img/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("E006 Validación: Solo se permiten imágenes en formato JPG, JPEG, PNG o GIF.");
                }

                $newFileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadFile)) {
                    // Eliminar la imagen anterior si existe
                    if (isset($_POST['imagen_actual']) && !empty($_POST['imagen_actual'])) {
                        $oldImagePath = $uploadDir . $_POST['imagen_actual'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $data['imagen'] = $newFileName;
                } else {
                    throw new Exception("E006 Validación: Error al subir la imagen.");
                }
            } elseif (isset($_POST['imagen_actual'])) {
                // Mantener la imagen actual si no se subió una nueva
                $data['imagen'] = $_POST['imagen_actual'];
            }

            // Actualizar el producto
            if ($this->productModel->update($id, $data)) {
                setFlash('Producto actualizado correctamente', 'success');
                redirect('products');
            } else {
                throw new Exception("Error al actualizar el producto.");
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            if (isset($id)) {
                $product = $this->productModel->getById($id);
                $proveedores = $this->productModel->getProveedores();
                require_once VIEWS_PATH . '/products/edit.php';
                return;
            }
            redirect('products');
        }
    }

    public function delete($id) {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de producto no es válido.");
            }

            if ($this->productModel->delete($id)) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }
                setFlash('success', 'Producto eliminado exitosamente');
            } else {
                throw new Exception("Error al eliminar el producto.");
            }
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            $errors[] = $e->getMessage();
            $products = $this->productModel->getAll();
            require_once VIEWS_PATH . '/products/index.php';
            return;
        }
        redirect('products');
    }

    public function view($id) {
        $errors = [];
        $product = null;
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
                throw new Exception("E006 Validación: El ID de producto no es válido.");
            }
            $product = $this->productModel->getById($id);
            if (!$product) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $products = $this->productModel->getAll();
            require_once VIEWS_PATH . '/products/index.php';
            return;
        }
        require_once VIEWS_PATH . '/products/view.php';
    }
}
?>