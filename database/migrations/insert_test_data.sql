-- Insertar proveedores de prueba
INSERT INTO proveedores (nombre, contacto, email) VALUES
('Farmacia Central', 'Juan Pérez', 'juan@farmaciacentral.com'),
('Medicamentos Express', 'María García', 'maria@medicamentosexpress.com');

-- Insertar productos de prueba
INSERT INTO productos (nombre, descripcion, precio, fecha_caducidad, lote, stock, categoria, id_proveedor) VALUES
('Paracetamol 500mg', 'Analgésico y antipirético', 5.99, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 'LOT001', 5, 'medicamentos', 1),
('Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo', 7.99, DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 'LOT002', 8, 'medicamentos', 1),
('Amoxicilina 500mg', 'Antibiótico', 12.99, DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'LOT003', 15, 'medicamentos', 2);

-- Insertar movimientos de stock
INSERT INTO movimientosstock (id_producto, cantidad, tipo_movimiento, fecha, motivo) VALUES
(1, 10, 'entrada', NOW(), 'Compra inicial'),
(2, 15, 'entrada', NOW(), 'Compra inicial'),
(3, 20, 'entrada', NOW(), 'Compra inicial'),
(1, 5, 'salida', NOW(), 'Venta'),
(2, 7, 'salida', NOW(), 'Venta');

-- Insertar órdenes de compra
INSERT INTO ordenescompra (id_proveedor, fecha_orden, estado, total) VALUES
(1, NOW(), 'pendiente', 150.00),
(2, NOW(), 'pendiente', 200.00); 