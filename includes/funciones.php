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

// Función para redirigir con mensajes
function redirigir($url, $mensajeToast = null, $tipoToast = 'info') {
    // Si la URL ya es absoluta, usarla directamente
    if (preg_match('/^https?:\/\//', $url)) {
        $urlRedireccion = $url;
    }
    // Si la URL comienza con BASE_URL, usarla directamente
    else if (strpos($url, BASE_URL) === 0) {
        $urlRedireccion = $url;
    }
    else {
        // Determinar si estamos en un subdirectorio
        $scriptActual = $_SERVER['SCRIPT_NAME'];
        $enVistas = strpos($scriptActual, '/views/') !== false;
        $enControladores = strpos($scriptActual, '/controllers/') !== false;
        
        // Construir la URL completa
        $urlRedireccion = BASE_URL . $url;
    }
    
    // Añadir parámetros de toast si se proporcionan
    if ($mensajeToast !== null) {
        $separator = (strpos($urlRedireccion, '?') !== false) ? '&' : '?';
        $urlRedireccion .= $separator . 'toast_message=' . urlencode($mensajeToast) . '&toast_type=' . urlencode($tipoToast);
    }
    
    header('Location: ' . $urlRedireccion);
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
    $rutaCompleta = ROOT_PATH . $directorio;
    if (!verificarDirectorio($rutaCompleta)) {
        return false;
    }
    
    // Generar nombre único para el archivo
    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $rutaDestino = $rutaCompleta . $nombreArchivo;
    
    // Mover archivo al directorio de destino
    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        return $directorio . $nombreArchivo;
    }
    
    return false;
}

// Función para obtener la URL de la carátula de un juego
function obtenerUrlCaratula($juego) {
    if (!empty($juego['caratula'])) {
        // 1. Intentar con la ruta directa (como está almacenada en la BD)
        $rutaCompleta = ROOT_PATH . $juego['caratula'];
        if (file_exists($rutaCompleta)) {
            return BASE_URL . $juego['caratula'];
        }
        
        // 2. Intentar con la ruta basada en DIR_CARATULAS
        $rutaAlternativa = ROOT_PATH . DIR_CARATULAS . basename($juego['caratula']);
        if (file_exists($rutaAlternativa)) {
            return BASE_URL . DIR_CARATULAS . basename($juego['caratula']);
        }
        
        // 3. Si todo falla, devolver la ruta original (podría funcionar en algunos casos)
        return BASE_URL . $juego['caratula'];
    }
    return null;
}
?>
