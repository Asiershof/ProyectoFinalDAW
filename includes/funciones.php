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

// Función para redirigir - versión simple con rutas relativas
function redirigir($url) {
    header('Location: ' . $url);
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

// Función para subir imágenes
function subirImagen($archivo, $directorio = 'assets/uploads/') {
    // Verificar si se subió un archivo
    if (!isset($archivo['name']) || $archivo['error'] !== 0) {
        return false;
    }
    
    // Crear directorio si no existe
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    
    // Generar nombre único para el archivo
    $nombre_archivo = uniqid() . '_' . $archivo['name'];
    $ruta_destino = $directorio . $nombre_archivo;
    
    // Mover archivo al directorio de destino
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        return $ruta_destino;
    }
    
    return false;
}
?>

