<?php
session_start();
require_once 'config/configuracion.php';

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php');
}

include ROOT_PATH . 'views/layouts/header.php';

// Conectar a la base de datos
global $conn;

// Obtener todos los juegos del usuario actual
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT id, titulo, caratula FROM videojuegos WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$juegos = [];
while ($row = $result->fetch_assoc()) {
    $juegos[] = $row;
}

// Procesar la solicitud de corrección
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['corregir'])) {
    $contador = 0;
    
    foreach ($juegos as $juego) {
        if (!empty($juego['caratula'])) {
            $ruta_fisica = ROOT_PATH . $juego['caratula'];
            
            // Verificar si el archivo existe
            if (!file_exists($ruta_fisica) && file_exists(ROOT_PATH . 'assets/uploads/caratulas/' . basename($juego['caratula']))) {
                // Corregir la ruta en la base de datos
                $nueva_ruta = 'assets/uploads/caratulas/' . basename($juego['caratula']);
                $sql_update = "UPDATE videojuegos SET caratula = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("si", $nueva_ruta, $juego['id']);
                $stmt_update->execute();
                $contador++;
            }
        }
    }
    
    $mensaje = "Se han corregido $contador rutas de imágenes.";
    
    // Recargar los juegos después de la corrección
    $stmt->execute();
    $result = $stmt->get_result();
    
    $juegos = [];
    while ($row = $result->fetch_assoc()) {
        $juegos[] = $row;
    }
}
?>

<main>
    <section class="form-container">
        <h2>Verificación de Imágenes</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <p>Esta herramienta verifica y corrige las rutas de las imágenes de tus juegos.</p>
        
        <form method="POST" action="">
            <div class="form-group div-btn">
                <button type="submit" name="corregir" class="btn btn-primary">Corregir Rutas de Imágenes</button>
            </div>
        </form>
        
        <h3>Tus Juegos con Imágenes</h3>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Título</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Ruta de Imagen</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Imagen Existe</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Vista Previa</th>
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
        
        <div class="form-group div-btn" style="margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>views/perfil.php" class="btn btn-primary">Volver al Perfil</a>
        </div>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

