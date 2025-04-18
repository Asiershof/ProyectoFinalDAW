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

// Procesar la solicitud de corrección
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['corregir'])) {
    // Actualizar los registros donde caratula es '0' a NULL
    $sql = "UPDATE videojuegos SET caratula = NULL WHERE caratula = '0'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $mensaje = "Se han corregido " . $conn->affected_rows . " registros en la base de datos.";
}

// Obtener estadísticas
$sql = "SELECT COUNT(*) as total, SUM(CASE WHEN caratula = '0' THEN 1 ELSE 0 END) as con_cero, SUM(CASE WHEN caratula IS NULL THEN 1 ELSE 0 END) as con_null FROM videojuegos";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();
?>

<main>
    <section class="form-container">
        <h2>Corrección de Base de Datos</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <p>Esta herramienta corrige los registros en la base de datos donde la columna 'caratula' tiene un valor '0'.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3>Estadísticas</h3>
            <p><strong>Total de juegos:</strong> <?php echo $stats['total']; ?></p>
            <p><strong>Juegos con caratula = '0':</strong> <?php echo $stats['con_cero']; ?></p>
            <p><strong>Juegos con caratula = NULL:</strong> <?php echo $stats['con_null']; ?></p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group div-btn">
                <button type="submit" name="corregir" class="btn btn-primary">Corregir Registros</button>
            </div>
        </form>
        
        <div class="form-group div-btn" style="margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>views/perfil.php" class="btn btn-primary">Volver al Perfil</a>
        </div>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

