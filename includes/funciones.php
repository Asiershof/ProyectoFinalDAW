<?php
function sanitizar($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function redirigir($url, $mensajeToast = null, $tipoToast = 'info') {
    if (preg_match('/^https?:\/\//', $url)) {
        $urlRedireccion = $url;
    }
    else if (strpos($url, BASE_URL) === 0) {
        $urlRedireccion = $url;
    }
    else {
        $scriptActual = $_SERVER['SCRIPT_NAME'];
        $enVistas = strpos($scriptActual, '/views/') !== false;
        $enControladores = strpos($scriptActual, '/controllers/') !== false;
        
        $urlRedireccion = BASE_URL . $url;
    }
    
    if ($mensajeToast !== null) {
        $separator = (strpos($urlRedireccion, '?') !== false) ? '&' : '?';
        $urlRedireccion .= $separator . 'toast_message=' . urlencode($mensajeToast) . '&toast_type=' . urlencode($tipoToast);
    }
    
    header('Location: ' . $urlRedireccion);
    exit;
}

function mostrarMensaje($mensaje, $tipo = 'error') {
    if ($tipo === 'error') {
        echo '<div class="mensaje error">' . $mensaje . '</div>';
    } else {
        echo '<div class="mensaje exito">' . $mensaje . '</div>';
    }
}

function verificarDirectorio($ruta) {
    if (!file_exists($ruta)) {
        return mkdir($ruta, 0777, true);
    }
    return true;
}

function subirImagen($archivo, $directorio = 'assets/uploads/') {
    if (!isset($archivo['name']) || $archivo['error'] !== 0) {
        return false;
    }
    
    $rutaCompleta = ROOT_PATH . $directorio;
    if (!verificarDirectorio($rutaCompleta)) {
        return false;
    }
    
    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $rutaDestino = $rutaCompleta . $nombreArchivo;
    
    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        return $directorio . $nombreArchivo;
    }
    
    return false;
}

function obtenerUrlCaratula($juego) {
    if (empty($juego['caratula'])) {
        return null;
    }
    
    $rutaCompleta = ROOT_PATH . $juego['caratula'];
    if (file_exists($rutaCompleta)) {
        return BASE_URL . $juego['caratula'];
    }
    
    $rutaAlternativa = ROOT_PATH . DIR_CARATULAS . basename($juego['caratula']);
    if (file_exists($rutaAlternativa)) {
        return BASE_URL . DIR_CARATULAS . basename($juego['caratula']);
    }
    
    $rutaAnterior = ROOT_PATH . 'uploads/caratulas/' . basename($juego['caratula']);
    if (file_exists($rutaAnterior)) {
        return BASE_URL . 'uploads/caratulas/' . basename($juego['caratula']);
    }
    
    return null;
}
?>
