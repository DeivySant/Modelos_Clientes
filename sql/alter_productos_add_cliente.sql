-- Agrega la columna `cliente` a la tabla `productos` para relacionarla con `clientes`.
-- Ejecuta este script en tu BD `restaurante` (por ejemplo en phpMyAdmin).
--
-- Recomendado: que `clientes.identificacion` sea el identificador del cliente.
-- En `productos.cliente` se guardará esa misma identificación.

ALTER TABLE productos
  ADD COLUMN cliente VARCHAR(64) NULL;

-- Opcional (mejora performance de joins/búsquedas)
CREATE INDEX idx_productos_cliente ON productos (cliente);

-- Opcional (solo si ambas tablas usan InnoDB y los tipos coinciden)
-- ALTER TABLE productos
--   ADD CONSTRAINT fk_productos_clientes
--   FOREIGN KEY (cliente) REFERENCES clientes(identificacion)
--   ON UPDATE CASCADE
--   ON DELETE SET NULL;
