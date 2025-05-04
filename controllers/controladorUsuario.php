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
            $nombreUsuario = sanitizar($_POST['nombre_usuario']); // Cambiado: $nombre_usuario a $nombreUsuario
            $correo = sanitizar($_POST['correo']);
            $password = sanitizar($_POST['password']);
            $confirmarPassword = sanitizar($_POST['confirmar_password']); // Cambiado: $confirmar_password a $confirmarPassword
            
            // Validaciones básicas
            if (empty($nombreUsuario) || empty($correo) || empty($password)) { // Cambiado: $nombre_usuario a $nombreUsuario
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            if ($password !== $confirmarPassword) { // Cambiado: $confirmar_password a $confirmarPassword
                return ['error' => 'Las contraseñas no coinciden'];
            }
            
            if (strlen($password) < 6) {
                return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
            }
            
            // Intentar registrar al usuario
            $resultado = $this->modelo->registrar($nombreUsuario, $correo, $password); // Cambiado: $nombre_usuario a $nombreUsuario
            
            if ($resultado) {
                // Iniciar sesión automáticamente
                $_SESSION['usuario_id'] = $resultado;
                $_SESSION['nombre_usuario'] = $nombreUsuario; // Cambiado: $nombre_usuario a $nombreUsuario
                
                // Redirigir al index con mensaje toast
                return [
                    'exito' => 'Registro exitoso', 
                    'redirigir' => BASE_URL . 'index.php',
                    'toast_message' => '¡Bienvenido a Mi Biblioteca de Juegos! Tu cuenta ha sido creada correctamente.',
                    'toast_type' => 'success'
                ];
            } else {
                return ['error' => 'El nombre de usuario o correo ya está en uso'];
            }
        }
        
        return [];
    }
    
    // Procesar inicio de sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreUsuario = sanitizar($_POST['nombre_usuario']); // Cambiado: $nombre_usuario a $nombreUsuario
            $password = $_POST['password'];
            
            if (empty($nombreUsuario) || empty($password)) { // Cambiado: $nombre_usuario a $nombreUsuario
                return ['error' => 'Todos los campos son obligatorios'];
            }
            
            $usuario = $this->modelo->login($nombreUsuario, $password); // Cambiado: $nombre_usuario a $nombreUsuario
            
            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                
                // Redirigir al index con mensaje toast
                return [
                    'exito' => 'Inicio de sesión exitoso', 
                    'redirigir' => BASE_URL . 'index.php',
                    'toast_message' => '¡Bienvenido de nuevo, ' . $usuario['nombre_usuario'] . '!',
                    'toast_type' => 'success'
                ];
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
        
        return [
            'exito' => 'Sesión cerrada correctamente', 
            'redirigir' => BASE_URL . 'index.php',
            'toast_message' => '¡Has cerrado sesión correctamente!',
            'toast_type' => 'info'
        ];
    }
    
    // Obtener datos del usuario actual
    public function obtenerUsuarioActual() {
        if (isset($_SESSION['usuario_id'])) {
            return $this->modelo->obtenerPorId($_SESSION['usuario_id']);
        }
        
        return false;
    }
    
    // Actualizar avatar del usuario
    public function actualizarAvatar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            // Verificar si el usuario está logueado
            if (!estaLogueado()) {
                return ['error' => 'Debes iniciar sesión para cambiar tu avatar'];
            }
            
            $idUsuario = $_SESSION['usuario_id']; // Cambiado: $id_usuario a $idUsuario
            $usuario = $this->modelo->obtenerPorId($idUsuario); // Cambiado: $id_usuario a $idUsuario
            
            // Verificar si se subió un archivo
            if ($_FILES['avatar']['error'] !== 0) {
                return ['error' => 'Error al subir la imagen'];
            }
            
            // Verificar el tipo de archivo
            $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['avatar']['type'], $tipos_permitidos)) {
                return ['error' => 'Formato de archivo no permitido. Se permiten: JPG, PNG, GIF, WEBP'];
            }
            
            // Verificar el tamaño (máximo 1MB)
            if ($_FILES['avatar']['size'] > 1048576) { // 1MB
                return ['error' => 'La imagen no debe superar 1MB'];
            }
            
            // Crear directorio de avatares si no existe
            $rutaAvatares = RUTA_AVATARES;
            if (!file_exists($rutaAvatares)) {
                mkdir($rutaAvatares, 0777, true);
            }
            
            // Generar nombre único para el archivo
            $nombreArchivo = uniqid() . '_' . basename($_FILES['avatar']['name']); // Cambiado: $nombre_archivo a $nombreArchivo
            $rutaDestino = $rutaAvatares . $nombreArchivo; // Cambiado: $nombre_archivo a $nombreArchivo, $ruta_destino a $rutaDestino
            $rutaRelativa = DIR_AVATARES . $nombreArchivo; // Cambiado: $nombre_archivo a $nombreArchivo, $ruta_relativa a $rutaRelativa
            
            // Mover el archivo subido
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $rutaDestino)) { // Cambiado: $ruta_destino a $rutaDestino
                // ELIMINAR AVATAR ANTERIOR si no es el de por defecto
                if (!empty($usuario['avatar']) && $usuario['avatar'] !== 'assets/img/usuario.png') {
                    $rutaAnterior = ROOT_PATH . $usuario['avatar']; // Cambiado: $ruta_anterior a $rutaAnterior
                    if (file_exists($rutaAnterior)) { // Cambiado: $ruta_anterior a $rutaAnterior
                        unlink($rutaAnterior); // Cambiado: $ruta_anterior a $rutaAnterior
                    }
                }
                // Actualizar la ruta del avatar en la base de datos
                $resultado = $this->modelo->actualizarAvatar($idUsuario, $rutaRelativa); // Cambiado: $id_usuario a $idUsuario, $ruta_relativa a $rutaRelativa
                
                if ($resultado) {
                    // En lugar de retornar, redirigir con mensaje toast
                    redirigir(BASE_URL . 'views/perfil.php', 
                        '¡Tu avatar ha sido actualizado correctamente!', 
                        'success');
                    exit;
                } else {
                    return ['error' => 'Error al actualizar el avatar en la base de datos'];
                }
            } else {
                return ['error' => 'Error al guardar la imagen'];
            }
        }
        
        return [];
    }

    /**
     * Procesa la edición del perfil de usuario (correo, contraseña, avatar).
     * @return array Resultado con 'exito/redirigir/toast_message' o 'error'.
     */
    public function editarPerfil() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [];
        }
        
        $usuarioActual = $this->obtenerUsuarioActual(); // Cambiado: $usuario_actual a $usuarioActual
        $idUsuario = $usuarioActual['id']; // Cambiado: $id_usuario a $idUsuario, $usuario_actual a $usuarioActual

        // --- 1. Sanitizar y Validar Nombre de Usuario ---
        $nombreUsuario = sanitizar($_POST['nombre_usuario']);
        if (empty($nombreUsuario)) {
            return ['error' => 'El nombre de usuario no puede estar vacío.'];
        }
        
        if ($nombreUsuario !== $usuarioActual['nombre_usuario']) { // Cambiado: $usuario_actual a $usuarioActual
            if (!$this->modelo->actualizarNombreUsuario($idUsuario, $nombreUsuario)) { // Cambiado: $id_usuario a $idUsuario
                return ['error' => 'Error al actualizar el nombre de usuario o ya está en uso.'];
            }
            // Nombre de usuario actualizado correctamente
        }
        
        // --- 2. Sanitizar y Validar Correo ---
        $correo = sanitizar($_POST['correo']);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'El formato del correo electrónico no es válido.'];
        }
        
        if ($correo !== $usuarioActual['correo_electronico']) { // Cambiado: $usuario_actual a $usuarioActual
            if (!$this->modelo->actualizarEmail($idUsuario, $correo)) { // Cambiado: $id_usuario a $idUsuario
                return ['error' => 'Error al actualizar el correo o ya está en uso.'];
            }
            // Correo actualizado correctamente
        }

        // --- 3. Validar y Actualizar Contraseña (si se intenta cambiar) ---
        $passwordActual = sanitizar($_POST['current_password']); // Cambiado: $current_password a $passwordActual
        $nuevoPassword = sanitizar($_POST['new_password']); // Cambiado: $new_password a $nuevoPassword
        $confirmarNuevoPassword = sanitizar($_POST['confirm_new_password']); // Cambiado: $confirm_new_password a $confirmarNuevoPassword

        if (!empty($passwordActual) || !empty($nuevoPassword) || !empty($confirmarNuevoPassword)) { // Cambiado: variables
            if (empty($passwordActual) || empty($nuevoPassword) || empty($confirmarNuevoPassword)) { // Cambiado: variables
                return ['error' => 'Para cambiar la contraseña, debes rellenar los campos: actual, nueva y confirmación.'];
            }
            
            if (strlen($nuevoPassword) < 6) { // Cambiado: $new_password a $nuevoPassword
                return ['error' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
            }
            
            if ($nuevoPassword !== $confirmarNuevoPassword) { // Cambiado: variables
                return ['error' => 'La nueva contraseña y su confirmación no coinciden.'];
            }
            
            if (!$this->modelo->actualizarPassword($idUsuario, $passwordActual, $nuevoPassword)) { // Cambiado: variables
                return ['error' => 'Error al actualizar la contraseña. Verifica que la contraseña actual sea correcta.'];
            }
            // Contraseña actualizada correctamente
        }

        // --- 4. Procesar Subida de Avatar (si se subió uno nuevo) ---
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $archivoAvatar = $_FILES['avatar']; // Cambiado: $archivo_avatar a $archivoAvatar
            
            // Validaciones
            $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']; // Cambiado: $tipos_permitidos a $tiposPermitidos
            $tamanoMaximo = 1048576; // 1MB // Cambiado: $max_size a $tamanoMaximo

            if (!in_array($archivoAvatar['type'], $tiposPermitidos)) { // Cambiado: variables
                return ['error' => 'Formato de avatar no permitido. Se permiten: JPG, PNG, GIF, WEBP.'];
            }
            
            if ($archivoAvatar['size'] > $tamanoMaximo) { // Cambiado: variables
                return ['error' => 'La imagen del avatar no debe superar 1MB.'];
            }

            // Crear directorio si no existe
            $rutaAvatares = RUTA_AVATARES;
            if (!file_exists($rutaAvatares)) { // Cambiado: $ruta_avatares a $rutaAvatares
                if (!mkdir($rutaAvatares, 0777, true)) { // Cambiado: $ruta_avatares a $rutaAvatares
                    return ['error' => 'Error al crear el directorio para avatares.'];
                }
            }
            
            // Generar nombre único y rutas
            $nombreArchivo = uniqid('avatar_') . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "_", basename($archivoAvatar['name'])); // Cambiado: $nombre_archivo a $nombreArchivo, $archivo_avatar a $archivoAvatar
            $rutaDestino = $rutaAvatares . $nombreArchivo; // Cambiado: $ruta_destino a $rutaDestino, $ruta_avatares a $rutaAvatares, $nombre_archivo a $nombreArchivo
            $rutaRelativa = DIR_AVATARES . $nombreArchivo; // Cambiado: $ruta_relativa a $rutaRelativa, $nombre_archivo a $nombreArchivo

            if (move_uploaded_file($archivoAvatar['tmp_name'], $rutaDestino)) { // Cambiado: $archivo_avatar a $archivoAvatar, $ruta_destino a $rutaDestino
                // Eliminar avatar anterior si existe y no es el default
                $avatarAnterior = $usuarioActual['avatar']; // Cambiado: $avatar_anterior a $avatarAnterior, $usuario_actual a $usuarioActual
                if (!empty($avatarAnterior) && $avatarAnterior !== 'assets/img/usuario.png') { // Cambiado: $avatar_anterior a $avatarAnterior
                    $rutaAnteriorCompleta = ROOT_PATH . $avatarAnterior; // Cambiado: $ruta_anterior_completa a $rutaAnteriorCompleta, $avatar_anterior a $avatarAnterior
                    if (file_exists($rutaAnteriorCompleta)) { // Cambiado: $ruta_anterior_completa a $rutaAnteriorCompleta
                        @unlink($rutaAnteriorCompleta); // Cambiado: $ruta_anterior_completa a $rutaAnteriorCompleta
                    }
                }
                
                if (!$this->modelo->actualizarAvatar($idUsuario, $rutaRelativa)) { // Cambiado: $id_usuario a $idUsuario, $ruta_relativa a $rutaRelativa
                    @unlink($rutaDestino); // Borrar archivo subido si falla la actualización en BBDD // Cambiado: $ruta_destino a $rutaDestino
                    return ['error' => 'Error al guardar la ruta del nuevo avatar en la base de datos.'];
                }
                // Avatar actualizado correctamente
            } else {
                return ['error' => 'Error al mover el archivo del avatar subido.'];
            }
        }

        // --- 5. Si todo fue bien ---
        return [
            'exito' => 'Perfil actualizado correctamente',
            'redirigir' => BASE_URL . 'views/perfil.php',
            'toast_message' => 'Perfil actualizado correctamente',
            'toast_type' => 'success'
        ];
    }
} // Fin de la clase ControladorUsuario
?>
