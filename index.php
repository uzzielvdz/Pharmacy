<?php
require_once __DIR__ . '/config/config.php';

$controllers = [
    'product' => __DIR__ . '/controllers/ProductController.php',
    'proveedor' => __DIR__ . '/controllers/ProveedorController.php',
    'movimiento' => __DIR__ . '/controllers/MovimientoStockController.php',
    'orden' => __DIR__ . '/controllers/OrdenCompraController.php',
    'dashboard' => __DIR__ . '/controllers/DashboardController.php'
];

$controller = filter_input(INPUT_GET, 'controller', FILTER_SANITIZE_STRING) ?? 'dashboard';
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? 'index';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? null;

try {
    if (!isset($controllers[$controller]) || !file_exists($controllers[$controller])) {
        throw new Exception("E007 Sistema: El controlador solicitado no es válido.");
    }
    require_once $controllers[$controller];

    if ($controller === 'dashboard') {
        $controllerInstance = new DashboardController();
        switch ($action) {
            case 'index':
                $controllerInstance->index();
                break;
            default:
                throw new Exception("E007 Sistema: La acción solicitada no es válida.");
        }
    } elseif ($controller === 'product') {
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
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'update':
                $controllerInstance->update();
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'view':
                if ($id) {
                    $controllerInstance->view($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            default:
                throw new Exception("E007 Sistema: La acción solicitada no es válida.");
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
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'update':
                $controllerInstance->update();
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            default:
                throw new Exception("E007 Sistema: La acción solicitada no es válida.");
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
            case 'edit':
                if ($id) {
                    $controllerInstance->edit($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'update':
                $controllerInstance->update();
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            default:
                throw new Exception("E007 Sistema: La acción solicitada no es válida.");
        }
    } elseif ($controller === 'orden') {
        $controllerInstance = new OrdenCompraController();
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
            case 'completar':
                if ($id) {
                    $controllerInstance->completar($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'cancelar':
                if ($id) {
                    $controllerInstance->cancelar($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            case 'delete':
                if ($id) {
                    $controllerInstance->delete($id);
                } else {
                    throw new Exception("E006 Validación: El ID proporcionado no es válido.");
                }
                break;
            default:
                throw new Exception("E007 Sistema: La acción solicitada no es válida.");
        }
    }
} catch (Exception $e) {
    $errors = [$e->getMessage()];
    require_once __DIR__ . '/views/error.php';
} 