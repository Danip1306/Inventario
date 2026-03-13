CREATE DATABASE IF NOT EXISTS inventario_db;

USE inventario_db;

CREATE TABLE IF NOT EXISTS productos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    codigo       VARCHAR(20)    NOT NULL UNIQUE,
    nombre       VARCHAR(100)   NOT NULL,
    categoria    VARCHAR(50)    NOT NULL,
    precio       DECIMAL(10,2)  NOT NULL CHECK (precio >= 0),
    stock        INT            NOT NULL DEFAULT 0 CHECK (stock >= 0),
    stock_minimo INT            NOT NULL DEFAULT 5,
    proveedor    VARCHAR(100),
    fecha_reg    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    actualizado  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Datos de ejemplo 
INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, proveedor) VALUES
('PROD-001', 'Shampoo Antipulgas para Perros',      'Higiene',        25000,  12, 5, 'PetCare Colombia'),
('PROD-002', 'Collar Ajustable para Perro',         'Accesorios',     18000,   3, 5, 'DogStyle SAS'),
('PROD-003', 'Cepillo Deslanador para Mascotas',    'Higiene',        22000,   1, 5, 'PetTools Colombia'),
('PROD-004', 'Cortaúñas Profesional para Perros',   'Higiene',        15000,   8, 5, 'VetCare S.A.'),
('PROD-005', 'Concentrado Premium para Perros 2kg', 'Alimento',       45000,   0, 5, 'NutriPet Colombia'),
('PROD-006', 'Arena Sanitaria para Gatos 5kg',      'Higiene',        32000,  15, 5, 'CatLife SAS'),
('PROD-007', 'Juguete Mordedor para Perros',        'Juguetes',       12000,   4, 5, 'DogPlay Colombia'),
('PROD-008', 'Correa Resistente para Perros',       'Accesorios',     20000,   6, 5, 'DogStyle SAS');