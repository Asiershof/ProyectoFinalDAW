<?php
// Definir la ruta absoluta al directorio raíz del proyecto
define('ROOT_PATH', dirname(__DIR__) . '/');

// Método mejorado para determinar la URL base
function obtenerBaseUrl() {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    
    // Obtener la ruta base del proyecto
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = '';
    
    // Si estamos en un subdirectorio, extraer la ruta base
    if (strpos($script_name, 'views/') !== false) {
        $base_dir = substr($script_name, 0, strpos($script_name, 'views/'));
    } elseif (strpos($script_name, 'controllers/') !== false) {
        $base_dir = substr($script_name, 0, strpos($script_name, 'controllers/'));
    } else {
        $base_dir = dirname($script_name);
    }
    
    // Asegurarse de que la ruta termine con una barra
    $base_dir = rtrim($base_dir, '/') . '/';
    
    // Si estamos en la raíz del servidor web, ajustar la ruta
    if ($base_dir === '//') {
        $base_dir = '/';
    }
    
    return $protocolo . $host . $base_dir;
}

// Definir la URL base como una constante
define('BASE_URL', obtenerBaseUrl());

// Definir constantes para uploads
define('UPLOADS_DIR', 'assets/uploads/');
define('UPLOADS_PATH', ROOT_PATH . UPLOADS_DIR);
define('CARATULAS_DIR', UPLOADS_DIR . 'caratulas/');
define('CARATULAS_PATH', ROOT_PATH . CARATULAS_DIR);

// Incluir archivos necesarios
require_once ROOT_PATH . 'config/conexion.php';
require_once ROOT_PATH . 'includes/funciones.php';

// Crear la estructura de carpetas si no existe
if (!file_exists(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0777, true);
}
if (!file_exists(CARATULAS_PATH)) {
    mkdir(CARATULAS_PATH, 0777, true);
    
    // Crear un archivo index.html para proteger el directorio
    $index_content = '<!DOCTYPE html><html><head><title>Acceso Denegado</title></head><body><h1>Acceso Denegado</h1><p>No tienes permiso para ver el contenido de este directorio.</p></body></html>';
    file_put_contents(CARATULAS_PATH . 'index.html', $index_content);
}

// Función para depurar
function debug($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
?>
