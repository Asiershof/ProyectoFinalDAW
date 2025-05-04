<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';

// Redirigir si no está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php', 'Debes iniciar sesión para editar tu perfil', 'warning');
}

$controladorUsuario = new ControladorUsuario();
$usuario = $controladorUsuario->obtenerUsuarioActual();

// Verificar que se obtuvo el usuario correctamente
if (!$usuario) {
    redirigir(BASE_URL . 'views/login.php', 'Error al obtener tus datos. Por favor, inicia sesión nuevamente.', 'error');
}

// Determinar la URL del avatar actual para la vista previa
$avatar_url = BASE_URL . 'assets/img/usuario.png';
if (!empty($usuario['avatar'])) {
    $avatar_path = ROOT_PATH . $usuario['avatar'];
    if (file_exists($avatar_path)) {
        $avatar_url = BASE_URL . $usuario['avatar'];
    }
}

// Procesar el formulario si es un POST
$resultado = $controladorUsuario->editarPerfil();

// Manejar redirecciones si hay éxito
if (isset($resultado['exito']) && isset($resultado['redirigir'])) {
    $toast_message = isset($resultado['toast_message']) ? $resultado['toast_message'] : null;
    $toast_type = isset($resultado['toast_type']) ? $resultado['toast_type'] : 'info';
    redirigir($resultado['redirigir'], $toast_message, $toast_type);
}

// Incluir la vista
include ROOT_PATH . 'views/editarPerfil.php';
?>