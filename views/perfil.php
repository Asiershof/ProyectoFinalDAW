<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorUsuario.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Redirigir si no está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php');
}

$controladorUsuario = new ControladorUsuario();
$usuario = $controladorUsuario->obtenerUsuarioActual();

$controladorJuego = new ControladorJuego();
$juegos = $controladorJuego->obtenerJuegosUsuario();

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="perfil-container">
        <h2>Perfil de <?php echo $usuario['nombre_usuario']; ?></h2>
        
        <div class="info-usuario">
            <p><strong>Correo:</strong> <?php echo $usuario['correo_electronico']; ?></p>
            <p><strong>Fecha de registro:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
            <p><strong>Total de juegos completados:</strong> <?php echo count($juegos); ?></p>
        </div>
        
        <div class="acciones">
            <a href="<?php echo BASE_URL; ?>views/anyadirJuego.php" class="btn btn-primary">Añadir nuevo juego</a>
        </div>
    </section>
    
    <section class="juegos-container">
        <h3>Mis juegos completados</h3>
        
        <?php if (empty($juegos)): ?>
            <p class="mensaje info">Aún no has añadido ningún juego. ¡Comienza a registrar tus juegos completados!</p>
        <?php else: ?>
            <div class="grid-juegos">
                <?php foreach ($juegos as $juego): ?>
                    <div class="tarjeta-juego">
                        <div class="caratula">
                            <?php 
                            $mostrar_imagen = false;
                            if (!empty($juego['caratula']) && $juego['caratula'] !== '0') {
                                $ruta_fisica = ROOT_PATH . $juego['caratula'];
                                if (file_exists($ruta_fisica)) {
                                    $mostrar_imagen = true;
                                }
                            }
                            
                            if ($mostrar_imagen): 
                            ?>
                                <img src="<?php echo BASE_URL . $juego['caratula']; ?>" alt="Carátula de <?php echo $juego['titulo']; ?>">
                            <?php else: ?>
                                <div class="sin-caratula">Sin imagen</div>
                            <?php endif; ?>
                        </div>
                        <div class="info-juego">
                            <h4><?php echo $juego['titulo']; ?></h4>
                            <p><strong>Plataforma:</strong> <?php echo $juego['plataforma']; ?></p>
                            <p><strong>Fecha inicio:</strong> <?php echo date('d/m/Y', strtotime($juego['fecha_inicio'])); ?></p>
                            <p><strong>Fecha fin:</strong> <?php echo date('d/m/Y', strtotime($juego['fecha_fin'])); ?></p>
                            <p><strong>Horas jugadas:</strong> <?php echo $juego['horas_jugadas']; ?></p>
                            <div class="acciones-juego">
                                <a href="<?php echo BASE_URL; ?>controllers/editarJuego.php?id=<?php echo $juego['id']; ?>" class="btn btn-primary" style="margin-right: 5px;">Editar</a>
                                <a href="<?php echo BASE_URL; ?>controllers/eliminarJuego.php?id=<?php echo $juego['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este juego?')">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>
