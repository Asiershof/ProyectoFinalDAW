<?php
class Usuario {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Registrar un nuevo usuario
    public function registrar($nombre_usuario, $correo, $password) {
        // Verificar si el usuario ya existe
        $stmt = $this->conexion->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ?");
        $stmt->bind_param("ss", $nombre_usuario, $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Usuario o correo ya existe
        }
        
        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar nuevo usuario
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasenya) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_usuario, $correo, $password_hash);
        
        if ($stmt->execute()) {
            return $this->conexion->insert_id;
        }
        
        return false;
    }
    
    // Iniciar sesión
    public function login($nombre_usuario, $password) {
        $stmt = $this->conexion->prepare("SELECT id, nombre_usuario, contrasenya FROM usuarios WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            if (password_verify($password, $usuario['contrasenya'])) {
                return $usuario;
            }
        }
        
        return false;
    }
    
    // Obtener datos de un usuario por ID
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT id, nombre_usuario, correo_electronico, fecha_registro FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
}
?>

