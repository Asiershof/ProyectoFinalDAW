</div>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Mi Biblioteca de Juegos. Todos los derechos reservados.</p>
        </div>
    </footer>
    <?php
    // Determinar si estamos en la raíz o en una subcarpeta
    $en_raiz = (strpos($_SERVER['PHP_SELF'], '/index.php') !== false);
    $ruta_js = $en_raiz ? 'assets/js/main.js' : '../assets/js/main.js';
    ?>
    <script src="<?php echo $ruta_js; ?>"></script>
</body>
</html>

