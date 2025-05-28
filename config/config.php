<?php
// Definir la ruta base del proyecto
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/Pharmacy');

// Definir rutas de directorios
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('MODELS_PATH', BASE_PATH . '/models');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pharmacy');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Farmacia');
define('APP_VERSION', '1.0.0');

// Configuración de sesión
session_start();

// Función helper para rutas
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Función helper para assets
function asset($path = '') {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// Función helper para vistas
function view($path, $data = []) {
    extract($data);
    ob_start();
    require VIEWS_PATH . '/' . $path . '.php';
    return ob_get_clean();
}

// Función helper para redirecciones
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

// Función helper para mensajes flash
function setFlash($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Función helper para obtener mensajes flash
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>