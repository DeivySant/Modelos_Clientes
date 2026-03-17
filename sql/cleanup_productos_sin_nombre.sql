-- Elimina filas inválidas en productos (sin nombre).
-- Úsalo si te aparece un registro "en blanco" y no lo necesitas.

DELETE FROM productos
WHERE Nombre = '' OR Nombre IS NULL;

