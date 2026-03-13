
USE inventario_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    username      VARCHAR(50)   NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    rol           ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    activo        TINYINT(1)    NOT NULL DEFAULT 1,
    ultimo_acceso TIMESTAMP     NULL,
    fecha_reg     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre, email, username, password_hash, rol)
VALUES (
    'Administrador',
    'admin@inventariopro.com',
    'admin',
    '$2y$12$FXtT3vJfi6HYOsOQ2NM04OsYzREpRWnY14COraUoi/y...',  -- Admin123!
    'admin'
)
ON DUPLICATE KEY UPDATE id = id;