<?php
require_once ROOT_PATH . 'models/Usuario.php';

class ControladorUsuario {
    private $modelo;
    
    public function __construct() {
        global $conn;
        $this->modelo = new Usuario($conn);
    }
    
    // Procesar registro de usuario
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_usuario = sanitizar($_POST['nombre_usuario']);
            $correo = sanitizar($_POST['correo']);
            $password = $_POST['password'];
            $confirmar_password = $_POST['confirmar_password'];
            
            // Validaciones básicas
            if (empty($nombre_usuario) || empty($correo) || empty($password)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            if ($password !== $confirmar_password) {
                return ['error' => 'Las contraseñas no coinciden'];
            }
            
            if (strlen($password) < 6) {
                return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
            }
            
            // Intentar registrar al usuario
            $resultado = $this->modelo->registrar($nombre_usuario, $correo, $password);
            
            if ($resultado) {
                // Iniciar sesión automáticamente
                $_SESSION['usuario_id'] = $resultado;
                $_SESSION['nombre_usuario'] = $nombre_usuario;
                
                // Redirigir al index en lugar de al perfil
                return ['exito' => 'Registro exitoso', 'redirigir' => '../index.php'];
            } else {
                return ['error' => 'El nombre de usuario o correo ya está en uso'];
            }
        }
        
        return [];
    }
    
    // Procesar inicio de sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_usuario = sanitizar($_POST['nombre_usuario']);
            $password = $_POST['password'];
            
            if (empty($nombre_usuario) || empty($password)) {
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            $usuario = $this->modelo->login($nombre_usuario, $password);
            
            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                
                // Redirigir al index en lugar de al perfil
                return ['exito' => 'Inicio de sesión exitoso', 'redirigir' => '../index.php'];
            } else {
                return ['error' => 'Nombre de usuario o contraseña incorrectos'];
            }
        }
        
        return [];
    }
    
    // Cerrar sesión
    public function logout() {
        session_unset();
        session_destroy();
        
        return ['exito' => 'Sesión cerrada correctamente', 'redirigir' => '../index.php'];
    }
    
    // Obtener datos del usuario actual
    public function obtenerUsuarioActual() {
        if (isset($_SESSION['usuario_id'])) {
            return $this->modelo->obtenerPorId($_SESSION['usuario_id']);
        }
        
        return false;
    }
}
?>

