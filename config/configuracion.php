<?php
define('ROOT_PATH', dirname(__DIR__) . '/');

function obtenerBaseUrl() {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = '';
    
    if (strpos($script_name, 'views/') !== false) {
        $base_dir = substr($script_name, 0, strpos($script_name, 'views/'));
    } elseif (strpos($script_name, 'controllers/') !== false) {
        $base_dir = substr($script_name, 0, strpos($script_name, 'controllers/'));
    } else {
        $base_dir = dirname($script_name);
    }
    
    $base_dir = rtrim($base_dir, '/') . '/';
    
    if ($base_dir === '//') {
        $base_dir = '/';
    }
    
    return $protocolo . $host . $base_dir;
}

define('BASE_URL', obtenerBaseUrl());

define('DIR_DESCARGAS', 'assets/uploads/');
define('RUTA_DESCARGAS', ROOT_PATH . DIR_DESCARGAS);
define('DIR_CARATULAS', 'assets/uploads/caratulas/');
define('RUTA_CARATULAS', ROOT_PATH . DIR_CARATULAS);
define('DIR_AVATARES', 'assets/uploads/avatares/');
define('RUTA_AVATARES', ROOT_PATH . DIR_AVATARES);

require_once ROOT_PATH . 'config/conexion.php';
require_once ROOT_PATH . 'includes/funciones.php';

if (!file_exists(RUTA_DESCARGAS)) {
    mkdir(RUTA_DESCARGAS, 0777, true);
}
if (!file_exists(RUTA_CARATULAS)) {
    mkdir(RUTA_CARATULAS, 0777, true);
    
    $index_content = '<!DOCTYPE html><html><head><title>Acceso Denegado</title></head><body><h1>Acceso Denegado</h1><p>No tienes permiso para ver el contenido de este directorio.</p></body></html>';
    file_put_contents(RUTA_CARATULAS . 'index.html', $index_content);
}

function debug($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
?>
