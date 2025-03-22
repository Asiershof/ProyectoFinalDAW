<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    redirigir('../views/login.php');
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirigir('../views/perfil.php');
}

$id = (int)$_GET['id'];
$controlador = new ControladorJuego();
$resultado = $controlador->eliminar($id);

// Redirigir de vuelta al perfil
redirigir('../views/perfil.php');
?>

