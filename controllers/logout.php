<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';

$controlador = new ControladorUsuario();
$resultado = $controlador->logout();

redirigir(BASE_URL . 'index.php');
?>
