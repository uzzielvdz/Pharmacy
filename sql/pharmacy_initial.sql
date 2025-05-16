-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS farmacia;
USE farmacia;

-- Tabla Proveedores
CREATE TABLE Proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    email VARCHAR(100)
);

-- Tabla Productos
CREATE TABLE Productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2),
    fecha_caducidad DATE,
    lote VARCHAR(50),
    stock INT NOT NULL DEFAULT 0,
    id_proveedor INT,
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Tabla OrdenesCompra
CREATE TABLE OrdenesCompra (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_proveedor INT,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Tabla DetallesOrdenes
CREATE TABLE DetallesOrdenes (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT,
    id_producto INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (id_orden) REFERENCES OrdenesCompra(id_orden) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Tabla MovimientosStock
CREATE TABLE MovimientosStock (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    cantidad INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    fecha DATETIME NOT NULL,
    motivo VARCHAR(100),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Tabla Usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('farmacéutico', 'cajero', 'auditor') NOT NULL,
    contrasena VARCHAR(255) NOT NULL
);

-- Tabla Auditoria
CREATE TABLE Auditoria (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    accion VARCHAR(100),
    tabla_afectada VARCHAR(50),
    fecha DATETIME NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Índices
CREATE INDEX idx_productos_nombre ON Productos(nombre);
CREATE INDEX idx_productos_caducidad ON Productos(fecha_caducidad);
CREATE INDEX idx_movimientos_fecha ON MovimientosStock(fecha);

-- Disparador para actualizar stock
DELIMITER //
CREATE TRIGGER actualizar_stock
AFTER INSERT ON MovimientosStock
FOR EACH ROW
BEGIN
    IF NEW.tipo_movimiento = 'entrada' THEN
        UPDATE Productos
        SET stock = stock + NEW.cantidad
        WHERE id_producto = NEW.id_producto;
    ELSEIF NEW.tipo_movimiento = 'salida' THEN
        IF (SELECT stock FROM Productos WHERE id_producto = NEW.id_producto) < NEW.cantidad THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente';
        ELSE
            UPDATE Productos
            SET stock = stock - NEW.cantidad
            WHERE id_producto = NEW.id_producto;
        END IF;
    END IF;
END //
DELIMITER ;

-- Disparador para bloquear movimientos de productos caducados
DELIMITER //
CREATE TRIGGER bloquear_caducados
BEFORE INSERT ON MovimientosStock
FOR EACH ROW
BEGIN
    DECLARE fecha_cad DATE;
    SELECT fecha_caducidad INTO fecha_cad
    FROM Productos
    WHERE id_producto = NEW.id_producto;
    
    IF NEW.tipo_movimiento = 'salida' AND fecha_cad < CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede registrar salida de un producto caducado';
    END IF;
END //
DELIMITER ;

-- Vista de Productos Próximos a Vencer
CREATE VIEW ProductosPorVencer AS
SELECT id_producto, nombre, fecha_caducidad, stock
FROM Productos
WHERE fecha_caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY);

-- Vista de Stock Bajo
CREATE VIEW StockBajo AS
SELECT p.id_producto, p.nombre, p.stock, pr.nombre AS proveedor
FROM Productos p
JOIN Proveedores pr ON p.id_proveedor = pr.id_proveedor
WHERE p.stock < 10;

-- Procedimiento para Registrar Venta
DELIMITER //
CREATE PROCEDURE RegistrarVenta(
    IN p_id_producto INT,
    IN p_cantidad INT,
    IN p_motivo VARCHAR(100)
)
BEGIN
    DECLARE stock_actual INT;
    SELECT stock INTO stock_actual
    FROM Productos
    WHERE id_producto = p_id_producto;
    
    IF stock_actual >= p_cantidad THEN
        START TRANSACTION;
        INSERT INTO MovimientosStock (id_producto, cantidad, tipo_movimiento, fecha, motivo)
        VALUES (p_id_producto, p_cantidad, 'salida', NOW(), p_motivo);
        COMMIT;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para la venta';
    END IF;
END //
DELIMITER ;

-- Procedimiento para Generar Orden de Compra
DELIMITER //
CREATE PROCEDURE GenerarOrdenCompra(
    IN p_id_proveedor INT
)
BEGIN
    DECLARE nuevo_id_orden INT;
    
    INSERT INTO OrdenesCompra (fecha, id_proveedor, estado)
    VALUES (CURDATE(), p_id_proveedor, 'pendiente');
    
    SET nuevo_id_orden = LAST_INSERT_ID();
    
    INSERT INTO DetallesOrdenes (id_orden, id_producto, cantidad, precio_unitario)
    SELECT nuevo_id_orden, id_producto, 50, precio
    FROM Productos
    WHERE id_proveedor = p_id_proveedor AND stock < 10;
END //
DELIMITER ;