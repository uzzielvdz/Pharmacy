-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS pharmacy;
USE pharmacy;

-- Tabla de Proveedores
CREATE TABLE IF NOT EXISTS Proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('nacional', 'internacional') NOT NULL,
    telefono VARCHAR(20),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    direccion TEXT,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de Productos
CREATE TABLE IF NOT EXISTS Productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 5,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    fecha_caducidad DATE,
    id_proveedor INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor)
);

-- Tabla de Movimientos de Stock
CREATE TABLE IF NOT EXISTS MovimientosStock (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INT NOT NULL,
    fecha DATETIME NOT NULL,
    motivo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);

-- Tabla de Órdenes de Compra
CREATE TABLE IF NOT EXISTS OrdenesCompra (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT NOT NULL,
    fecha_orden DATE NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    total DECIMAL(10,2) NOT NULL,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor)
);

-- Insertar algunos datos de prueba
INSERT INTO Proveedores (nombre, tipo, telefono, direccion) VALUES
('Farmacia Central', 'nacional', '123-456-7890', 'Calle Principal 123'),
('Medicamentos Express', 'internacional', '987-654-3210', 'Avenida Global 456');

INSERT INTO Productos (nombre, descripcion, stock, stock_minimo, precio_compra, precio_venta, id_proveedor) VALUES
('Paracetamol 500mg', 'Analgésico y antipirético', 100, 20, 0.50, 1.00, 1),
('Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo', 50, 15, 0.75, 1.50, 1),
('Amoxicilina 500mg', 'Antibiótico', 30, 10, 1.00, 2.00, 2);

INSERT INTO MovimientosStock (id_producto, tipo_movimiento, cantidad, fecha, motivo) VALUES
(1, 'entrada', 100, NOW(), 'Compra inicial'),
(2, 'entrada', 50, NOW(), 'Compra inicial'),
(3, 'entrada', 30, NOW(), 'Compra inicial');

INSERT INTO OrdenesCompra (id_proveedor, fecha_orden, estado, total) VALUES
(1, CURDATE(), 'pendiente', 150.00),
(2, CURDATE(), 'pendiente', 200.00); 