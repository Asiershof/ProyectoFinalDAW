<?php
session_start();
require_once 'config/configuracion.php';
include ROOT_PATH . 'views/layouts/header.php';
?>

<main class="py-5 my-5">
    <div class="container">
        <section class="mb-5">
            <div class="bg-white rounded shadow-sm p-4 text-center">
                <h2 class="mb-3">¿Qué es esta página?</h2>
                <p class="lead">
                    Esta página te permite guardar y gestionar los datos de los videojuegos que has completado. 
                    Podrás registrar el título, la fecha de inicio y fin, las horas jugadas, la plataforma y subir una imagen de la carátula del juego.
                </p>
            </div>
        </section>
        
        <section>
            <h3 class="mb-4 text-center">Características principales</h3>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Registro de juegos</h4>
                            <p class="card-text">Guarda todos los detalles de los juegos que has completado, incluyendo el tiempo que te ha llevado terminarlos.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Carátulas personalizadas</h4>
                            <p class="card-text">Sube imágenes de las carátulas de tus juegos para tener una biblioteca visual atractiva.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Seguimiento de plataformas</h4>
                            <p class="card-text">Organiza tus juegos por plataforma para ver en cuál has jugado más títulos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include ROOT_PATH . 'views/layouts/footer.php'; ?>

