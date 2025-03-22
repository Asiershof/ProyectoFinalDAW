-- Crear la base de datos
CREATE DATABASE bibliotecaJuegos;

-- Usar la base de datos recién creada
USE bibliotecaJuegos;

-- Crear la tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    correo_electronico VARCHAR(100) NOT NULL UNIQUE,
    contrasenya VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear la tabla de videojuegos
CREATE TABLE videojuegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    horas_jugadas INT NOT NULL,
    plataforma VARCHAR(50) NOT NULL,
    caratula VARCHAR(255), -- Ruta donde se almacenará la imagen de la carátula
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);
