<?php
// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Configurar manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');

// Configurar codificación
mb_internal_encoding('UTF-8');

// Función para cargar clases automáticamente
spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Buscar en diferentes directorios
    $directories = [
        CONTROLLERS_PATH,
        MODELS_PATH,
        BASE_PATH . '/core'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . DIRECTORY_SEPARATOR . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Función para manejar errores
function handleError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    
    // Registrar error en log
    error_log(print_r($error, true));
    
    // Mostrar error en desarrollo
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo '<pre>';
        print_r($error);
        echo '</pre>';
    }
    
    return true;
}

// Configurar manejador de errores
set_error_handler('handleError');

// Función para manejar excepciones no capturadas
function handleException($exception) {
    $error = [
        'type' => get_class($exception),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
    
    // Registrar error en log
    error_log(print_r($error, true));
    
    // Mostrar error en desarrollo
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo '<pre>';
        print_r($error);
        echo '</pre>';
    }
}

// Configurar manejador de excepciones
set_exception_handler('handleException'); 