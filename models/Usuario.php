<?php
class Usuario {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Registrar un nuevo usuario
    public function registrar($nombreUsuario, $correo, $password) {
        // Hash del password antes de almacenarlo
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasenya) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sss", $nombreUsuario, $correo, $passwordHash);
        
        return $stmt->execute();
    }
    
    // Iniciar sesión
    public function login($nombreUsuario, $password) {
        $stmt = $this->conexion->prepare("SELECT id, nombre_usuario, contrasenya FROM usuarios WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombreUsuario);
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
    
    // Actualizar avatar del usuario
    public function actualizarAvatar($idUsuario, $rutaAvatar) {
        $stmt = $this->conexion->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $rutaAvatar, $idUsuario);
        
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    // Obtener datos de un usuario por ID
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT id, nombre_usuario, correo_electronico, fecha_registro, avatar FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }

    // Actualizar correo electrónico
    public function actualizarEmail($id, $nuevoCorreo) {
        // Verificar si el nuevo correo ya está en uso por OTRO usuario
        $stmt_check = $this->conexion->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? AND id != ?");
        $stmt_check->bind_param("si", $nuevoCorreo, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            return false; // Correo ya en uso
        }

        $stmt = $this->conexion->prepare("UPDATE usuarios SET correo_electronico = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoCorreo, $id);
        return $stmt->execute(); // Devuelve true si la ejecución fue exitosa
    }

    // Actualizar contraseña
    public function actualizarPassword($id, $passwordActual, $nuevoPassword) {
        // 1. Obtener la contraseña actual hasheada del usuario
        $stmt_get = $this->conexion->prepare("SELECT contrasenya FROM usuarios WHERE id = ?");
        $stmt_get->bind_param("i", $id);
        $stmt_get->execute();
        $result_get = $stmt_get->get_result();
        
        if ($result_get->num_rows === 1) {
            $usuario = $result_get->fetch_assoc();
            // 2. Verificar si la contraseña actual proporcionada coincide
            if (password_verify($passwordActual, $usuario['contrasenya'])) {
                // 3. Hashear la nueva contraseña
                $nuevoPasswordHash = password_hash($nuevoPassword, PASSWORD_DEFAULT);
                // 4. Actualizar la contraseña en la base de datos
                $stmt_update = $this->conexion->prepare("UPDATE usuarios SET contrasenya = ? WHERE id = ?");
                $stmt_update->bind_param("si", $nuevoPasswordHash, $id);
                return $stmt_update->execute(); // Devuelve true si la ejecución fue exitosa
            }
        }
        return false; // La contraseña actual no coincide o el usuario no existe
    }

    // Actualizar nombre de usuario
    public function actualizarNombreUsuario($id, $nuevoNombre) {
        // Verificar si el nuevo nombre ya está en uso por OTRO usuario
        $stmt_check = $this->conexion->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? AND id != ?");
        $stmt_check->bind_param("si", $nuevoNombre, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            return false; // Nombre ya en uso
        }

        // Actualizar nombre de usuario
        $stmt = $this->conexion->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoNombre, $id);
        $result = $stmt->execute();
        
        if ($result) {
            // También actualiza el nombre en la sesión si el cambio fue exitoso
            $_SESSION['nombre_usuario'] = $nuevoNombre;
        }
        
        return $result; // Devuelve true si la ejecución fue exitosa
    }

    // Actualizar perfil del usuario
    public function actualizarPerfil($idUsuario, $nombreUsuario, $correo, $rutaAvatar = null) {
        // Si hay avatar, actualizarlo también
        if ($rutaAvatar !== null) {
            $sql = "UPDATE usuarios SET nombre_usuario = ?, correo_electronico = ?, avatar = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("sssi", $nombreUsuario, $correo, $rutaAvatar, $idUsuario);
        } else {
            // Actualizar solo nombre y correo
            $sql = "UPDATE usuarios SET nombre_usuario = ?, correo_electronico = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ssi", $nombreUsuario, $correo, $idUsuario);
        }
        
        return $stmt->execute();
    }
}
?>
