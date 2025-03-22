<?php
require_once ROOT_PATH . 'models/Juego.php';

class ControladorJuego {
    private $modelo;
    
    public function __construct() {
        global $conn;
        $this->modelo = new Juego($conn);
    }
    
    // Procesar añadir juego
    public function añadir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si el usuario está logueado
            if (!estaLogueado()) {
                return ['error' => 'Debes iniciar sesión para añadir juegos', 'redirigir' => 'login.php'];
            }
            
            $titulo = sanitizar($_POST['titulo']);
            $fecha_inicio = sanitizar($_POST['fecha_inicio']);
            $fecha_fin = sanitizar($_POST['fecha_fin']);
            $horas_jugadas = (int)sanitizar($_POST['horas_jugadas']);
            $plataforma = sanitizar($_POST['plataforma']);
            $id_usuario = $_SESSION['usuario_id'];
            
            // Validaciones básicas
            if (empty($titulo) || empty($fecha_inicio) || empty($fecha_fin) || empty($horas_jugadas) || empty($plataforma)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            // Procesar imagen de carátula
            $caratula = '';
            if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === 0) {
                // Asegurarse de que el directorio existe
                $directorio = '../assets/uploads/caratulas/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                
                // Generar nombre único para el archivo
                $nombre_archivo = uniqid() . '_' . $_FILES['caratula']['name'];
                $ruta_destino = $directorio . $nombre_archivo;
                
                // Mover archivo al directorio de destino
                if (move_uploaded_file($_FILES['caratula']['tmp_name'], $ruta_destino)) {
                    $caratula = 'assets/uploads/caratulas/' . $nombre_archivo;
                } else {
                    return ['error' => 'Error al subir la imagen de carátula'];
                }
            }
            
            // Añadir juego
            $resultado = $this->modelo->añadir($titulo, $fecha_inicio, $fecha_fin, $horas_jugadas, $plataforma, $caratula, $id_usuario);
            
            if ($resultado) {
                return ['exito' => 'Juego añadido correctamente', 'redirigir' => 'perfil.php'];
            } else {
                return ['error' => 'Error al añadir el juego'];
            }
        }
        
        return [];
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
        
        $resultado = $this->modelo->eliminar($id, $_SESSION['usuario_id']);
        
        if ($resultado) {
            return ['exito' => 'Juego eliminado correctamente'];
        } else {
            return ['error' => 'Error al eliminar el juego o no tienes permiso'];
        }
    }
}
?>

