<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';

if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php', 'Debes iniciar sesión para editar tu perfil', 'warning');
}

$controladorUsuario = new ControladorUsuario();
$usuario = $controladorUsuario->obtenerUsuarioActual();

if (!$usuario) {
    redirigir(BASE_URL . 'views/login.php', 'Error al obtener tus datos. Por favor, inicia sesión nuevamente.', 'error');
}

$avatar_url = BASE_URL . 'assets/img/usuario.png';
if (!empty($usuario['avatar'])) {
    $avatar_path = ROOT_PATH . $usuario['avatar'];
    if (file_exists($avatar_path)) {
        $avatar_url = BASE_URL . $usuario['avatar'];
    }
}

$resultado = $controladorUsuario->editarPerfil();

if (isset($resultado['exito']) && isset($resultado['redirigir'])) {
    $toast_message = isset($resultado['toast_message']) ? $resultado['toast_message'] : null;
    $toast_type = isset($resultado['toast_type']) ? $resultado['toast_type'] : 'info';
    redirigir($resultado['redirigir'], $toast_message, $toast_type);
}

include ROOT_PATH . 'views/editarPerfil.php';
?>