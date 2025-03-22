<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';

// Redirigir si ya está logueado
if (estaLogueado()) {
    redirigir('../index.php');
}

$controlador = new ControladorUsuario();
$resultado = $controlador->registrar();

// Manejar la redirección después del registro
if (isset($resultado['redirigir'])) {
    redirigir($resultado['redirigir']);
}

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="form-container">
        <h2>Registro de Usuario</h2>
        
        <?php if (isset($resultado['error'])): ?>
            <div class="mensaje error"><?php echo $resultado['error']; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            
            <div class="form-group">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirmar_password">Confirmar contraseña:</label>
                <input type="password" id="confirmar_password" name="confirmar_password" required>
            </div>
            
            <div class="form-group div-btn">
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>
        </form>
        
        <p class="form-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

