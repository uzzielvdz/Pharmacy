<?php
require_once dirname(__DIR__) . '/controllers/ProductController.php';
require_once dirname(__DIR__) . '/controllers/ProveedorController.php';
require_once dirname(__DIR__) . '/controllers/MovimientoStockController.php';

$controller = filter_input(INPUT_GET, 'controller', FILTER_SANITIZE_STRING) ?? 'product';
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 'index';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? null;

try {
    if ($controller === 'product') {
        $controllerInstance = new ProductController();
        switch ($action) {
            case 'index':
                $controllerInstance->index();
                break;
            case 'create':
                $controllerInstance->create();
                break;
            case 'store':
                $controllerInstance->store();
                break;
            case 'edit':
                if ($id) {
                    $controllerInstance->edit($id);
                } else {
                    throw new Exception("ID inválido");
                }
                break;
            case 'update':
                $controllerInstance->update();
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("ID inválido");
                }
                break;
            default:
                $controllerInstance->index();
        }
    } elseif ($controller === 'proveedor') {
        $controllerInstance = new ProveedorController();
        switch ($action) {
            case 'index':
                $controllerInstance->index();
                break;
            case 'create':
                $controllerInstance->create();
                break;
            case 'store':
                $controllerInstance->store();
                break;
            case 'edit':
                if ($id) {
                    $controllerInstance->edit($id);
                } else {
                    throw new Exception("ID inválido");
                }
                break;
            case 'update':
                $controllerInstance->update();
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("ID inválido");
                }
                break;
            default:
                $controllerInstance->index();
        }
    } elseif ($controller === 'movimiento') {
        $controllerInstance = new MovimientoStockController();
        switch ($action) {
            case 'index':
                $controllerInstance->index();
                break;
            case 'create':
                $controllerInstance->create();
                break;
            case 'store':
                $controllerInstance->store();
                break;
            default:
                $controllerInstance->index();
        }
    } else {
        throw new Exception("Controlador no válido");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>