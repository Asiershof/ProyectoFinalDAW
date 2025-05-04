<?php
require_once ROOT_PATH . 'models/Juego.php';

class ControladorJuego {
    private $modelo;
    
    public function __construct() {
        global $conn;
        $this->modelo = new Juego($conn);
        
        // Asegurarse de que la carpeta de carátulas existe
        $this->verificarCarpetaCaratulas();
    }
    
    // Método para verificar y crear la carpeta de carátulas si no existe
    private function verificarCarpetaCaratulas() {
        if (!file_exists(RUTA_CARATULAS)) {
            mkdir(RUTA_CARATULAS, 0777, true);
            
            // Crear un archivo index.html para proteger el directorio
            $index_content = '<!DOCTYPE html><html><head><title>Acceso Denegado</title></head><body><h1>Acceso Denegado</h1><p>No tienes permiso para ver el contenido de este directorio.</p></body></html>';
            file_put_contents(RUTA_CARATULAS . 'index.html', $index_content);
        }
    }
    
    // Procesar añadir juego
    public function anyadir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si el usuario está logueado
            if (!estaLogueado()) {
                return ['error' => 'Debes iniciar sesión para añadir juegos', 'redirigir' => BASE_URL . 'views/login.php'];
            }
            
            // Actualizar variables si hay necesidad
            $tituloJuego = sanitizar($_POST['titulo']);
            $fechaInicio = sanitizar($_POST['fecha_inicio']);
            $fechaFin = sanitizar($_POST['fecha_fin']);
            $horasJugadas = (int)sanitizar($_POST['horas_jugadas']);
            $plataforma = sanitizar($_POST['plataforma']);
            $puntuacion = sanitizar($_POST['puntuacion']);
            $resenia = sanitizar($_POST['resenya']);
            $idUsuario = $_SESSION['usuario_id'];
            
            // Validaciones básicas
            if (empty($tituloJuego) || empty($fechaInicio) || empty($fechaFin) || empty($horasJugadas) || empty($plataforma) || empty($puntuacion) || empty($resenia)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            // Validar puntuación
            if ($puntuacion < 1 || $puntuacion > 10) {
                return ['error' => 'La puntuación debe estar entre 1 y 10.'];
            }

            // Procesar imagen de carátula
            $caratula = null; // Inicializar como null en lugar de cadena vacía
            
            if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === 0) {
                // Verificar el tipo de archivo
                if (!in_array($_FILES['caratula']['type'], ['image/jpeg', 'image/jpg', 'image/webp', 'image/png', 'image/gif'])) {
                    return ['error' => 'Formato de archivo no permitido'];
                }
                
                // Generar nombre único para el archivo
                $nombreArchivo = uniqid() . '_' . basename($_FILES['caratula']['name']);
                $rutaDestino = RUTA_CARATULAS . $nombreArchivo;
                
                // Mover archivo al directorio de destino
                if (move_uploaded_file($_FILES['caratula']['tmp_name'], $rutaDestino)) {
                    // Guardar la ruta relativa en la base de datos
                    $caratula = RUTA_CARATULAS . $nombreArchivo;
                    
                    // Verificar que la ruta no sea vacía
                    if (empty($caratula)) {
                        $caratula = null;
                    }
                } else {
                    return ['error' => 'Error al subir la imagen de carátula'];
                }
            }
            
            // Añadir juego
            $resultado = $this->modelo->anyadir($tituloJuego, $fechaInicio, $fechaFin, $horasJugadas, $plataforma, $caratula, $puntuacion, $resenia, $idUsuario);
            
            if ($resultado) {
                return [
                    'exito' => 'Juego añadido correctamente', 
                    'redirigir' => BASE_URL . 'views/perfil.php',
                    'toast_message' => "¡El juego \"$tituloJuego\" ha sido añadido a tu biblioteca!",
                    'toast_type' => 'success'
                ];
            } else {
                return ['error' => 'Error al añadir el juego'];
            }
        }
        
        return [];
    }
    
    // Procesar editar juego
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si el usuario está logueado
            if (!estaLogueado()) {
                return ['error' => 'Debes iniciar sesión para editar juegos', 'redirigir' => BASE_URL . 'views/login.php'];
            }
            
            // Obtener el juego actual
            $juegoActual = $this->modelo->obtenerPorId($id);
            
            // Verificar si el juego existe y pertenece al usuario
            if (!$juegoActual || $juegoActual['id_usuario'] != $_SESSION['usuario_id']) {
                return ['error' => 'No tienes permiso para editar este juego', 'redirigir' => BASE_URL . 'views/perfil.php'];
            }
            
            // Actualizar variables si hay necesidad
            $titulo = sanitizar($_POST['titulo']);
            $fechaInicio = sanitizar($_POST['fecha_inicio']);
            $fechaFin = sanitizar($_POST['fecha_fin']);
            $horasJugadas = (int)sanitizar($_POST['horas_jugadas']);
            $plataforma = sanitizar($_POST['plataforma']);
            $puntuacion = sanitizar($_POST['puntuacion']);
            $resenia = sanitizar($_POST['resenya']);

            // Comprobar si no hay cambios respecto a los valores actuales
            $sinCambios = (
                $titulo === $juegoActual['titulo'] &&
                $fechaInicio === $juegoActual['fecha_inicio'] &&
                $fechaFin === $juegoActual['fecha_fin'] &&
                $horasJugadas == $juegoActual['horas_jugadas'] &&
                $plataforma === $juegoActual['plataforma'] &&
                $puntuacion == $juegoActual['puntuacion'] &&
                $resenia === $juegoActual['resenya'] &&
                (!isset($_FILES['caratula']) || $_FILES['caratula']['error'] !== 0)
            );

            if ($sinCambios) {
                return [
                    'error' => 'No has realizado ningún cambio en el juego'
                ];
            }
            
            // Validaciones básicas
            if (empty($titulo) || empty($fechaInicio) || empty($fechaFin) || empty($horasJugadas) || empty($plataforma) || empty($puntuacion) || empty($resenia)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            // Validar puntuación
            if ($puntuacion < 1 || $puntuacion > 10) {
                return ['error' => 'La puntuación debe estar entre 1 y 10'];
            }

            // Procesar imagen de carátula
            $caratula = $juegoActual['caratula']; // Mantener la carátula actual por defecto
            
            if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === 0) {
                // Verificar el tipo de archivo
                if (!in_array($_FILES['caratula']['type'], ['image/jpeg', 'image/jpg', 'image/webp', 'image/png', 'image/gif'])) {
                    return ['error' => 'Formato de archivo no permitido'];
                }
                
                // Generar nombre único para el archivo
                $nombreArchivo = uniqid() . '_' . basename($_FILES['caratula']['name']);
                $rutaDestino = RUTA_CARATULAS . $nombreArchivo;
                
                // Mover archivo al directorio de destino
                if (move_uploaded_file($_FILES['caratula']['tmp_name'], $rutaDestino)) {
                    // ELIMINAR CARÁTULA ANTERIOR si existe
                    if (!empty($juegoActual['caratula'])) {
                        $rutaAnterior = ROOT_PATH . $juegoActual['caratula'];
                        if (file_exists($rutaAnterior)) {
                            unlink($rutaAnterior);
                        }
                    }
                    
                    // Guardar la ruta relativa en la base de datos
                    $caratula = RUTA_CARATULAS . $nombreArchivo;
                } else {
                    return ['error' => 'Error al subir la imagen de carátula'];
                }
            }
            
            // Editar juego
            $resultado = $this->modelo->editar($id, $titulo, $fechaInicio, $fechaFin, $horasJugadas, $plataforma, $caratula, $puntuacion, $resenia);
            
            if ($resultado) {
                return [
                    'exito' => 'Juego actualizado correctamente',
                    'toast_message' => "¡El juego \"$titulo\" ha sido actualizado correctamente!",
                    'toast_type' => 'success'
                ];
            } else {
                return ['error' => 'Error al actualizar el juego'];
            }
        }
        
        return [];
    }
    
    // Obtener un juego específico
    public function obtenerJuego($id) {
        return $this->modelo->obtenerPorId($id);
    }
    
    // Obtener juegos del usuario actual
    public function obtenerJuegosUsuario() {
        if (estaLogueado()) {
            return $this->modelo->obtenerPorUsuario($_SESSION['usuario_id']);
        }
        
        return [];
    }
    
    // Eliminar juego
    public function eliminar($id) {
        if (!estaLogueado()) {
            return ['error' => 'Debes iniciar sesión para eliminar juegos'];
        }
        
        // Obtener información del juego para eliminar la imagen si existe
        $juego = $this->modelo->obtenerPorId($id);
        
        if (!$juego) {
            return [
                'error' => 'El juego no existe',
                'redirigir' => BASE_URL . 'views/perfil.php',
                'toast_message' => 'El juego que intentas eliminar no existe',
                'toast_type' => 'error'
            ];
        }
        
        $tituloJuego = $juego['titulo']; // Guardar el título para usarlo en el mensaje
        
        if ($juego && !empty($juego['caratula'])) {
            $rutaFisica = ROOT_PATH . $juego['caratula'];
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
        }
        
        $resultado = $this->modelo->eliminar($id, $_SESSION['usuario_id']);
        
        if ($resultado) {
            return [
                'exito' => 'Juego eliminado correctamente',
                'redirigir' => BASE_URL . 'views/perfil.php',
                'toast_message' => "El juego \"$tituloJuego\" ha sido eliminado de tu biblioteca",
                'toast_type' => 'info'
            ];
        } else {
            return [
                'error' => 'Error al eliminar el juego o no tienes permiso',
                'redirigir' => BASE_URL . 'views/perfil.php',
                'toast_message' => 'No se pudo eliminar el juego',
                'toast_type' => 'error'
            ];
        }
    }
}
?>
