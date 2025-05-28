-- Agregar columna categoria a la tabla Productos
ALTER TABLE Productos ADD COLUMN categoria VARCHAR(50) DEFAULT 'medicamentos' AFTER stock;

-- Actualizar los productos existentes con una categoría por defecto
UPDATE Productos SET categoria = 'medicamentos' WHERE categoria IS NULL; 