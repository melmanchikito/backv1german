-- Script para crear la base de datos y tabla de reservas
-- Ristorante Italini

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS ristorante CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ristorante;

-- Crear tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INT NOT NULL,
    ocasion VARCHAR(50),
    comentarios TEXT,
    estado ENUM('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Trigger para generar código único antes de insertar
DELIMITER $$

CREATE TRIGGER generar_codigo_reserva
BEFORE INSERT ON reservas
FOR EACH ROW
BEGIN
    SET NEW.codigo = CONCAT(
        'RES-',
        YEAR(CURDATE()),
        '-',
        LPAD(
            (SELECT IFNULL(MAX(id),0) + 1 FROM reservas),
            6,
            '0'
        )
    );
END$$

DELIMITER ;

-- Insertar datos de prueba (opcional)
INSERT INTO reservas 
(nombre, email, telefono, fecha, hora, personas, ocasion, comentarios)
VALUES
('Juan Pérez', 'juan@mail.com', '0991111111', '2025-02-10', '19:30:00', 2, 'aniversario', 'Mesa cerca de la ventana'),

('María López', 'maria@mail.com', '0982222222', '2025-02-11', '20:00:00', 4, 'cumpleaños', 'Traer pastel sorpresa'),

('Carlos Andrade', 'carlos@mail.com', '0973333333', '2025-02-12', '18:45:00', 3, 'casual', NULL),

('Ana Torres', 'ana@mail.com', '0964444444', '2025-02-13', '21:15:00', 5, 'negocios', 'Requiere proyector'),

('Luis Mendoza', 'luis@mail.com', '0955555555', '2025-02-14', '19:00:00', 2, NULL, NULL);

-- Verificar la creación
SELECT * FROM reservas;
