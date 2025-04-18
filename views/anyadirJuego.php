<?php
session_start();
require_once '../config/configuracion.php';
require_once ROOT_PATH . 'controllers/controladorJuego.php';

// Redirigir si no está logueado
if (!estaLogueado()) {
    redirigir(BASE_URL . 'views/login.php');
}

$controlador = new ControladorJuego();
$resultado = $controlador->añadir();

// Manejar la redirección después de añadir un juego
if (isset($resultado['redirigir'])) {
    redirigir($resultado['redirigir']);
}

include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="form-container">
        <h2>Añadir Juego Completado</h2>
        
        <?php if (isset($resultado['error'])): ?>
            <div class="mensaje error"><?php echo $resultado['error']; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título del juego:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_fin">Fecha de finalización:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>
            
            <div class="form-group">
                <label for="horas_jugadas">Horas jugadas:</label>
                <input type="number" id="horas_jugadas" name="horas_jugadas" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="plataforma">Plataforma:</label>
                <select id="plataforma" name="plataforma" required>
                    <option value="">Selecciona una plataforma</option>
                    <option value="PC">PC</option>
                    <option value="PlayStation 5">PlayStation 5</option>
                    <option value="PlayStation 4">PlayStation 4</option>
                    <option value="Xbox Series X/S">Xbox Series X/S</option>
                    <option value="Xbox One">Xbox One</option>
                    <option value="Nintendo Switch">Nintendo Switch</option>
                    <option value="Nintendo 3DS">Nintendo 3DS</option>
                    <option value="Mobile">Mobile</option>
                    <option value="Otra">Otra</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="caratula">Carátula del juego:</label>
                <input type="file" id="caratula" name="caratula" accept="image/*">
                <small>Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
            </div>
            
            <div class="form-group div-btn">
                <button type="submit" class="btn btn-primary">Añadir juego</button>
            </div>
        </form>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>
