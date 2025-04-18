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
        if (!file_exists(CARATULAS_PATH)) {
            mkdir(CARATULAS_PATH, 0777, true);
            
            // Crear un archivo index.html para proteger el directorio
            $index_content = '<!DOCTYPE html><html><head><title>Acceso Denegado</title></head><body><h1>Acceso Denegado</h1><p>No tienes permiso para ver el contenido de este directorio.</p></body></html>';
            file_put_contents(CARATULAS_PATH . 'index.html', $index_content);
        }
    }
    
    // Procesar añadir juego
    public function añadir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si el usuario está logueado
            if (!estaLogueado()) {
                return ['error' => 'Debes iniciar sesión para añadir juegos', 'redirigir' => BASE_URL . 'views/login.php'];
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
            $caratula = null; // Inicializar como null en lugar de cadena vacía
            
            if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === 0) {
                // Generar nombre único para el archivo
                $nombre_archivo = uniqid() . '_' . basename($_FILES['caratula']['name']);
                $ruta_destino = CARATULAS_PATH . $nombre_archivo;
                
                // Mover archivo al directorio de destino
                if (move_uploaded_file($_FILES['caratula']['tmp_name'], $ruta_destino)) {
                    // Guardar la ruta relativa en la base de datos
                    $caratula = CARATULAS_DIR . $nombre_archivo;
                    
                    // Verificar que la ruta no sea vacía
                    if (empty($caratula)) {
                        $caratula = null;
                    }
                } else {
                    return ['error' => 'Error al subir la imagen de carátula'];
                }
            }
            
            // Añadir juego
            $resultado = $this->modelo->añadir($titulo, $fecha_inicio, $fecha_fin, $horas_jugadas, $plataforma, $caratula, $id_usuario);
            
            if ($resultado) {
                return ['exito' => 'Juego añadido correctamente', 'redirigir' => BASE_URL . 'views/perfil.php'];
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
            $juego_actual = $this->modelo->obtenerPorId($id);
            
            // Verificar si el juego existe y pertenece al usuario
            if (!$juego_actual || $juego_actual['id_usuario'] != $_SESSION['usuario_id']) {
                return ['error' => 'No tienes permiso para editar este juego', 'redirigir' => BASE_URL . 'views/perfil.php'];
            }
            
            $titulo = sanitizar($_POST['titulo']);
            $fecha_inicio = sanitizar($_POST['fecha_inicio']);
            $fecha_fin = sanitizar($_POST['fecha_fin']);
            $horas_jugadas = (int)sanitizar($_POST['horas_jugadas']);
            $plataforma = sanitizar($_POST['plataforma']);
            
            // Validaciones básicas
            if (empty($titulo) || empty($fecha_inicio) || empty($fecha_fin) || empty($horas_jugadas) || empty($plataforma)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            // Procesar imagen de carátula
            $caratula = $juego_actual['caratula']; // Mantener la carátula actual por defecto
            
            if (isset($_FILES['caratula']) && $_FILES['caratula']['error'] === 0) {
                // Generar nombre único para el archivo
                $nombre_archivo = uniqid() . '_' . basename($_FILES['caratula']['name']);
                $ruta_destino = CARATULAS_PATH . $nombre_archivo;
                
                // Mover archivo al directorio de destino
                if (move_uploaded_file($_FILES['caratula']['tmp_name'], $ruta_destino)) {
                    // Si hay una carátula anterior, eliminarla
                    if (!empty($juego_actual['caratula']) && $juego_actual['caratula'] !== '0') {
                        $ruta_anterior = ROOT_PATH . $juego_actual['caratula'];
                        if (file_exists($ruta_anterior)) {
                            unlink($ruta_anterior);
                        }
                    }
                    
                    // Guardar la ruta relativa en la base de datos
                    $caratula = CARATULAS_DIR . $nombre_archivo;
                } else {
                    return ['error' => 'Error al subir la imagen de carátula'];
                }
            }
            
            // Editar juego
            $resultado = $this->modelo->editar($id, $titulo, $fecha_inicio, $fecha_fin, $horas_jugadas, $plataforma, $caratula);
            
            if ($resultado) {
                return ['exito' => 'Juego actualizado correctamente'];
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
        
        if ($juego && !empty($juego['caratula']) && $juego['caratula'] !== '0') {
            $ruta_fisica = ROOT_PATH . $juego['caratula'];
            if (file_exists($ruta_fisica)) {
                // Eliminar el archivo de imagen
                unlink($ruta_fisica);
            }
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
