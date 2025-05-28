-- Actualizar la tabla Productos
ALTER TABLE productos
ADD COLUMN categoria VARCHAR(50) DEFAULT 'medicamentos' AFTER stock_minimo,
ADD COLUMN lote VARCHAR(50) AFTER fecha_caducidad,
ADD COLUMN imagen VARCHAR(255) AFTER lote,
MODIFY COLUMN precio_compra DECIMAL(10,2) NULL,
MODIFY COLUMN precio_venta DECIMAL(10,2) NULL,
ADD COLUMN precio DECIMAL(10,2) NOT NULL AFTER stock_minimo;

-- Actualizar los datos existentes
UPDATE productos SET precio = precio_venta WHERE precio IS NULL;

-- Actualizar los datos existentes si es necesario
UPDATE productos SET categoria = 'medicamentos' WHERE categoria IS NULL; 