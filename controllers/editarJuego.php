<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php');
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirigir(BASE_URL . 'views/perfil.php');
}

$id = (int)$_GET['id'];
$controlador = new ControladorJuego();

// Obtener el juego a editar
$juego = $controlador->obtenerJuego($id);

// Verificar si el juego existe y pertenece al usuario actual
if (!$juego || $juego['id_usuario'] != $_SESSION['usuario_id']) {
    redirigir(BASE_URL . 'views/perfil.php');
}

// Procesar el formulario de edición
$resultado = $controlador->editar($id);

// Manejar la redirección después de editar un juego
if (isset($resultado['redirigir'])) {
    redirigir($resultado['redirigir']);
}

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="form-container">
        <h2>Editar Juego</h2>
        
        <?php if (isset($resultado['error'])): ?>
            <div class="mensaje error"><?php echo $resultado['error']; ?></div>
        <?php endif; ?>
        
        <?php if (isset($resultado['exito'])): ?>
            <div class="mensaje exito"><?php echo $resultado['exito']; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título del juego:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($juego['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $juego['fecha_inicio']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_fin">Fecha de finalización:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $juego['fecha_fin']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="horas_jugadas">Horas jugadas:</label>
                <input type="number" id="horas_jugadas" name="horas_jugadas" min="1" value="<?php echo $juego['horas_jugadas']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="plataforma">Plataforma:</label>
                <select id="plataforma" name="plataforma" required>
                    <option value="">Selecciona una plataforma</option>
                    <option value="PC" <?php echo ($juego['plataforma'] == 'PC') ? 'selected' : ''; ?>>PC</option>
                    <option value="PlayStation 5" <?php echo ($juego['plataforma'] == 'PlayStation 5') ? 'selected' : ''; ?>>PlayStation 5</option>
                    <option value="PlayStation 4" <?php echo ($juego['plataforma'] == 'PlayStation 4') ? 'selected' : ''; ?>>PlayStation 4</option>
                    <option value="Xbox Series X/S" <?php echo ($juego['plataforma'] == 'Xbox Series X/S') ? 'selected' : ''; ?>>Xbox Series X/S</option>
                    <option value="Xbox One" <?php echo ($juego['plataforma'] == 'Xbox One') ? 'selected' : ''; ?>>Xbox One</option>
                    <option value="Nintendo Switch" <?php echo ($juego['plataforma'] == 'Nintendo Switch') ? 'selected' : ''; ?>>Nintendo Switch</option>
                    <option value="Nintendo 3DS" <?php echo ($juego['plataforma'] == 'Nintendo 3DS') ? 'selected' : ''; ?>>Nintendo 3DS</option>
                    <option value="Mobile" <?php echo ($juego['plataforma'] == 'Mobile') ? 'selected' : ''; ?>>Mobile</option>
                    <option value="Otra" <?php echo ($juego['plataforma'] == 'Otra') ? 'selected' : ''; ?>>Otra</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="caratula">Carátula del juego:</label>
                <?php if (!empty($juego['caratula']) && $juego['caratula'] !== '0'): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo BASE_URL . $juego['caratula']; ?>" alt="Carátula actual" style="max-width: 100px; max-height: 100px;">
                        <p><small>Carátula actual. Sube una nueva imagen para reemplazarla.</small></p>
                    </div>
                <?php endif; ?>
                <input type="file" id="caratula" name="caratula" accept="image/*">
                <small>Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
            </div>
            
            <div class="form-group div-btn">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="<?php echo BASE_URL; ?>views/perfil.php" class="btn" style="margin-left: 10px; background-color: #6c757d;">Cancelar</a>
            </div>
        </form>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>
