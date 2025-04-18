<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';

// Redirigir si ya está logueado
if (estaLogueado()) {
    redirigir('index.php');
}

$controlador = new ControladorUsuario();
$resultado = $controlador->login();

// Manejar la redirección después del login
if (isset($resultado['redirigir'])) {
    redirigir($resultado['redirigir']);
}

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="form-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($resultado['error'])): ?>
            <div class="mensaje error"><?php echo $resultado['error']; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group div-btn">
                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
            </div>
        </form>
        
        <p class="form-link">¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>views/registro.php">Regístrate</a></p>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>
