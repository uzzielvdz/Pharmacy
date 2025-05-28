<?php
require_once dirname(__DIR__) . '/config/config.php';

class Product {
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

    public function getAll() {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.fecha_caducidad, p.lote, p.stock, p.categoria, pr.nombre AS proveedor 
                FROM productos p 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de productos.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.fecha_caducidad, p.lote, p.stock, p.categoria, p.id_proveedor, pr.nombre AS proveedor 
                FROM productos p 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
                WHERE p.id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$result) {
            throw new Exception("E001 Productos: Producto no encontrado.");
        }
        return $result;
    }

    public function checkNameExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM productos WHERE nombre = ?";
        if ($excludeId !== null) {
            $sql .= " AND id_producto != ?";
        }
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        if ($excludeId !== null) {
            $stmt->bind_param("si", $nombre, $excludeId);
        } else {
            $stmt->bind_param("s", $nombre);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result['count'] > 0) {
            throw new Exception("E001 Productos: Ya existe un producto con el nombre: " . htmlspecialchars($nombre) . ".");
        }
        return false;
    }

    public function checkHasMovements($id) {
        $sql = "SELECT COUNT(*) as count FROM movimientosstock WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result['count'] > 0) {
            throw new Exception("E001 Productos: No se puede eliminar el producto porque tiene movimientos de stock asociados.");
        }
        return false;
    }

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            $this->checkNameExists($data['nombre']);
            $sql = "INSERT INTO productos (nombre, descripcion, precio, fecha_caducidad, lote, stock, categoria, id_proveedor, imagen) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param(
                "ssdssssss",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['fecha_caducidad'],
                $data['lote'],
                $data['stock'],
                $data['categoria'],
                $data['id_proveedor'],
                $data['imagen'] ?? null
            );
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al crear el producto.");
            }
            $insertId = $this->conn->insert_id;
            $stmt->close();
            $this->conn->commit();
            return $insertId;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->conn->begin_transaction();
        try {
            $this->checkNameExists($data['nombre'], $id);
            
            // Construir la consulta SQL base
            $sql = "UPDATE productos SET 
                    nombre = ?, 
                    descripcion = ?, 
                    precio = ?, 
                    fecha_caducidad = ?, 
                    lote = ?, 
                    stock = ?, 
                    categoria = ?, 
                    id_proveedor = ?";
            
            $params = [
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['fecha_caducidad'],
                $data['lote'],
                $data['stock'],
                $data['categoria'],
                $data['id_proveedor']
            ];
            $types = "ssdssssi";

            // Agregar imagen si existe
            if (isset($data['imagen'])) {
                $sql .= ", imagen = ?";
                $params[] = $data['imagen'];
                $types .= "s";
            }

            $sql .= " WHERE id_producto = ?";
            $params[] = $id;
            $types .= "i";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }

            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al actualizar el producto.");
            }
            
            $stmt->close();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function delete($id) {
        $this->conn->begin_transaction();
        try {
            $this->checkHasMovements($id);
            $sql = "DELETE FROM productos WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result || $stmt->affected_rows === 0) {
                throw new Exception("E001 Productos: No se eliminó ningún producto con ID $id.");
            }
            $stmt->close();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getProveedores() {
        $sql = "SELECT id_proveedor, nombre FROM proveedores";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de proveedores.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>