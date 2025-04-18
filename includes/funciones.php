<?php
// Función para sanitizar entradas
function sanitizar($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Función para validar si el usuario está logueado
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

// Función para redirigir - versión mejorada
function redirigir($url) {
    // Si la URL ya es absoluta (comienza con http:// o https://), usarla directamente
    if (preg_match('/^https?:\/\//', $url)) {
        header('Location: ' . $url);
        exit;
    }
    
    // Si la URL comienza con BASE_URL, usarla directamente
    if (strpos($url, BASE_URL) === 0) {
        header('Location: ' . $url);
        exit;
    }
    
    // Determinar si estamos en un subdirectorio
    $current_script = $_SERVER['SCRIPT_NAME'];
    $in_views = strpos($current_script, '/views/') !== false;
    $in_controllers = strpos($current_script, '/controllers/') !== false;
    
    // Si la URL comienza con 'views/' y ya estamos en views, quitar 'views/'
    if ($in_views && strpos($url, 'views/') === 0) {
        $url = substr($url, 6); // Quitar 'views/'
    }
    
    // Si la URL comienza con 'controllers/' y ya estamos en controllers, quitar 'controllers/'
    if ($in_controllers && strpos($url, 'controllers/') === 0) {
        $url = substr($url, 12); // Quitar 'controllers/'
    }
    
    // Si la URL comienza con '/', quitarla para evitar problemas
    if (strpos($url, '/') === 0) {
        $url = substr($url, 1);
    }
    
    // Construir la URL completa
    $full_url = BASE_URL . $url;
    
    header('Location: ' . $full_url);
    exit;
}

// Función para mostrar mensajes de error o éxito
function mostrarMensaje($mensaje, $tipo = 'error') {
    if ($tipo === 'error') {
        echo '<div class="mensaje error">' . $mensaje . '</div>';
    } else {
        echo '<div class="mensaje exito">' . $mensaje . '</div>';
    }
}

// Función para verificar y crear directorios
function verificarDirectorio($ruta) {
    if (!file_exists($ruta)) {
        return mkdir($ruta, 0777, true);
    }
    return true;
}

// Función para subir imágenes
function subirImagen($archivo, $directorio = 'assets/uploads/') {
    // Verificar si se subió un archivo
    if (!isset($archivo['name']) || $archivo['error'] !== 0) {
        return false;
    }
    
    // Crear directorio si no existe
    $ruta_completa = ROOT_PATH . $directorio;
    if (!verificarDirectorio($ruta_completa)) {
        return false;
    }
    
    // Generar nombre único para el archivo
    $nombre_archivo = uniqid() . '_' . basename($archivo['name']);
    $ruta_destino = $ruta_completa . $nombre_archivo;
    
    // Mover archivo al directorio de destino
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        return $directorio . $nombre_archivo;
    }
    
    return false;
}
?>
