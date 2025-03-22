<?php
session_start();
require_once 'config/configuracion.php';
include ROOT_PATH . 'views/layouts/header.php';
?>

<main>
    <section class="bienvenida">
        <h2>¿Qué es esta página?</h2>
        <p>Esta página te permite guardar y gestionar los datos de los videojuegos que has completado. Podrás registrar el título, la fecha de inicio y fin, las horas jugadas, la plataforma y subir una imagen de la carátula del juego.</p>
    </section>
    
    <section class="caracteristicas">
        <h3>Características principales</h3>
        <div class="grid-caracteristicas">
            <div class="caracteristica">
                <h4>Registro de juegos</h4>
                <p>Guarda todos los detalles de los juegos que has completado, incluyendo el tiempo que te ha llevado terminarlos.</p>
            </div>
            <div class="caracteristica">
                <h4>Carátulas personalizadas</h4>
                <p>Sube imágenes de las carátulas de tus juegos para tener una biblioteca visual atractiva.</p>
            </div>
            <div class="caracteristica">
                <h4>Seguimiento de plataformas</h4>
                <p>Organiza tus juegos por plataforma para ver en cuál has jugado más títulos.</p>
            </div>
        </div>
    </section>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

