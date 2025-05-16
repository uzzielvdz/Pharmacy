<?php
require_once '../controllers/ProductController.php';

$controller = new ProductController();
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 'index';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? null;

try {
    switch ($action) {
        case 'index':
            $controller->index();
            break;
        case 'create':
            $controller->create();
            break;
        case 'edit':
            if ($id) {
                $controller->edit($id);
            } else {
                throw new Exception("ID inválido");
            }
            break;
        case 'delete':
            if ($id) {
                $controller->delete($id);
            } else {
                throw new Exception("ID inválido");
            }
            break;
        default:
            $controller->index();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>