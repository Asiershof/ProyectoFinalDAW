<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Juegos Completados</title>
    <?php
    // Determinar si estamos en la raíz o en una subcarpeta
    $en_raiz = (strpos($_SERVER['PHP_SELF'], '/index.php') !== false);
    $ruta_css = $en_raiz ? 'assets/css/indexStyle.css' : '../assets/css/indexStyle.css';
    ?>
    <link rel="stylesheet" href="<?php echo $ruta_css; ?>">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="<?php echo $en_raiz ? 'index.php' : '../index.php'; ?>">Mi Biblioteca de Juegos</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo $en_raiz ? 'index.php' : '../index.php'; ?>">Inicio</a></li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li><a href="<?php echo $en_raiz ? 'views/anyadirJuego.php' : 'anyadirJuego.php'; ?>">Añadir Juego</a></li>
                        <li class="usuario-dropdown">
                            <div class="usuario-info">
                                <span class="icono-usuario">👤</span>
                                <span class="nombre-usuario"><?php echo $_SESSION['nombre_usuario']; ?></span>
                            </div>
                            <div class="dropdown-menu">
                                <a href="<?php echo $en_raiz ? 'views/perfil.php' : 'perfil.php'; ?>">Mi Perfil</a>
                                <a href="<?php echo $en_raiz ? 'controllers/logout.php' : '../controllers/logout.php'; ?>">Cerrar sesión</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo $en_raiz ? 'views/login.php' : 'login.php'; ?>">Iniciar sesión</a></li>
                        <li><a href="<?php echo $en_raiz ? 'views/registro.php' : 'registro.php'; ?>">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">

