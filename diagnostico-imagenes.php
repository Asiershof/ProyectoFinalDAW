<?php
session_start();
require_once 'config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php');
}

// Obtener los juegos del usuario
$controlador = new ControladorJuego();
$juegos = $controlador->obtenerJuegosUsuario();

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="form-container">
        <h2>Diagnóstico de Imágenes</h2>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3>Información de Configuración</h3>
            <p><strong>ROOT_PATH:</strong> <?php echo ROOT_PATH; ?></p>
            <p><strong>BASE_URL:</strong> <?php echo BASE_URL; ?></p>
            <p><strong>UPLOADS_DIR:</strong> <?php echo UPLOADS_DIR; ?></p>
            <p><strong>UPLOADS_PATH:</strong> <?php echo UPLOADS_PATH; ?></p>
            <p><strong>CARATULAS_DIR:</strong> <?php echo CARATULAS_DIR; ?></p>
            <p><strong>CARATULAS_PATH:</strong> <?php echo CARATULAS_PATH; ?></p>
        </div>
        
        <?php if (empty($juegos)): ?>
            <p class="mensaje info">No hay juegos para mostrar.</p>
        <?php else: ?>
            <h3>Juegos con Carátulas</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Título</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Ruta en BD</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Ruta Completa</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Archivo Existe</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($juegos as $juego): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $juego['id']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $juego['titulo']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $juego['caratula']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php 
                                    if (!empty($juego['caratula'])) {
                                        echo ROOT_PATH . $juego['caratula'];
                                    } else {
                                        echo "N/A";
                                    }
                                ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php 
                                    if (!empty($juego['caratula'])) {
                                        $ruta_fisica = ROOT_PATH . $juego['caratula'];
                                        echo file_exists($ruta_fisica) ? "Sí" : "No";
                                    } else {
                                        echo "N/A";
                                    }
                                ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php if (!empty($juego['caratula'])): ?>
                                    <img src="<?php echo BASE_URL . $juego['caratula']; ?>" alt="Carátula" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    Sin imagen
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

