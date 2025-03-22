<?php
class Juego {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Añadir un nuevo juego
    public function añadir($titulo, $fecha_inicio, $fecha_fin, $horas_jugadas, $plataforma, $caratula, $id_usuario) {
        $stmt = $this->conexion->prepare("INSERT INTO videojuegos (titulo, fecha_inicio, fecha_fin, horas_jugadas, plataforma, caratula, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisii", $titulo, $fecha_inicio, $fecha_fin, $horas_jugadas, $plataforma, $caratula, $id_usuario);
        
        if ($stmt->execute()) {
            return $this->conexion->insert_id;
        }
        
        return false;
    }
    
    // Obtener todos los juegos de un usuario
    public function obtenerPorUsuario($id_usuario) {
        $stmt = $this->conexion->prepare("SELECT * FROM videojuegos WHERE id_usuario = ? ORDER BY fecha_fin DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $juegos = [];
        while ($juego = $result->fetch_assoc()) {
            $juegos[] = $juego;
        }
        
        return $juegos;
    }
    
    // Obtener un juego por ID
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM videojuegos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Eliminar un juego
    public function eliminar($id, $id_usuario) {
        $stmt = $this->conexion->prepare("DELETE FROM videojuegos WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id, $id_usuario);
        
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}
?>

