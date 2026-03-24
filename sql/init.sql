-- ═══════════════════════════════════════════════════════
-- INICIALIZACIÓN DE BASE DE DATOS RESTAURANTE
-- ═══════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurante;

-- ═══════════════════════════════════════════════════════
-- TABLA CLIENTES
-- ═══════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS clientes (
  identificacion VARCHAR(50) PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════
-- TABLA PRODUCTOS
-- ═══════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  cliente VARCHAR(64) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nombre (nombre),
  INDEX idx_productos_cliente (cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════
-- DATOS DE EJEMPLO - CLIENTES
-- ═══════════════════════════════════════════════════════
INSERT INTO clientes (identificacion, nombre, correo) VALUES
('44333', 'Camilo', 'casca@saxa.com'),
('89898', 'el buen gustin x2', 'juan@correo.com'),
('44444777', 'Jeison Agreda', 'agreda@gmail.com'),
('212121', 'Santiago', 'santiago@correo.com')
ON DUPLICATE KEY UPDATE 
  nombre = VALUES(nombre),
  correo = VALUES(correo);

-- ═══════════════════════════════════════════════════════
-- DATOS DE EJEMPLO - PRODUCTOS
-- ═══════════════════════════════════════════════════════
INSERT INTO productos (nombre, descripcion, valor) VALUES
('Pizza Margarita', 'Pizza con tomate, mozzarella y albahaca fresca', 15000.00),
('Hamburguesa Clásica', 'Hamburguesa con carne, lechuga, tomate y queso cheddar', 12000.00),
('Pasta Carbonara', 'Pasta con salsa carbonara, bacon y queso parmesano', 18000.00),
('Ensalada César', 'Ensalada con pollo, lechuga romana, croutones y aderezo césar', 10000.00)
ON DUPLICATE KEY UPDATE 
  descripcion = VALUES(descripcion),
  valor = VALUES(valor);

-- ═══════════════════════════════════════════════════════
-- EJECUTAR SCRIPTS ADICIONALES (si existen)
-- ═══════════════════════════════════════════════════════
-- Los archivos alter_productos_add_cliente.sql y cleanup_productos_sin_nombre.sql
-- pueden ejecutarse manualmente si son necesarios después de la inicialización
