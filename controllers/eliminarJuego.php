<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php', 'Debes iniciar sesión para eliminar juegos', 'warning');
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirigir(BASE_URL . 'views/perfil.php', 'No se especificó qué juego eliminar', 'error');
}

$id = (int)$_GET['id'];
$controlador = new ControladorJuego();
$resultado = $controlador->eliminar($id);

// Redirigir de vuelta al perfil con el mensaje toast apropiado
if (isset($resultado['redirigir'])) {
    $toast_message = isset($resultado['toast_message']) ? $resultado['toast_message'] : null;
    $toast_type = isset($resultado['toast_type']) ? $resultado['toast_type'] : 'info';
    redirigir($resultado['redirigir'], $toast_message, $toast_type);
} else {
    redirigir(BASE_URL . 'views/perfil.php');
}
?>
