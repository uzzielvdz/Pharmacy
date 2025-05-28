<?php
require_once dirname(__DIR__) . '/config/config.php';

class ProductoModel {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo conectar a la base de datos.");
        }
    }

    public function getProductosBajoStock() {
        $sql = "SELECT p.id_producto, p.nombre, p.stock, 10 as stock_minimo, pr.nombre as proveedor 
                FROM productos p 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
                WHERE p.stock < 10 
                ORDER BY p.stock ASC 
                LIMIT 5";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener productos con bajo stock.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductosProximosCaducar() {
        $sql = "SELECT id_producto, nombre, stock, fecha_caducidad 
                FROM productos 
                WHERE fecha_caducidad IS NOT NULL 
                AND fecha_caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY fecha_caducidad ASC 
                LIMIT 5";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener productos próximos a caducar.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalProductos() {
        $sql = "SELECT COUNT(*) as total FROM productos";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener el total de productos.");
        }
        return $result->fetch_assoc()['total'];
    }

    public function getTotalProductosCaducados() {
        $sql = "SELECT COUNT(*) as total 
                FROM productos 
                WHERE fecha_caducidad IS NOT NULL 
                AND fecha_caducidad < CURDATE()";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener el total de productos caducados.");
        }
        return $result->fetch_assoc()['total'];
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
} 