<?php
// Definir la ruta absoluta al directorio raíz del proyecto
define('ROOT_PATH', dirname(__DIR__) . '/');

// Calcular la URL base para enlaces relativos
$ruta_actual = $_SERVER['PHP_SELF'];
$nombre_archivo = basename($ruta_actual);
$ruta_directorio = str_replace($nombre_archivo, '', $ruta_actual);
$nivel_directorio = substr_count($ruta_directorio, '/');

// Construir la ruta relativa base
$ruta_base = '';
for ($i = 0; $i < $nivel_directorio - 1; $i++) {
    $ruta_base .= '../';
}

define('BASE_URL', $ruta_base);

// Incluir archivos necesarios
require_once ROOT_PATH . 'config/conexion.php';
require_once ROOT_PATH . 'includes/funciones.php';
?>

