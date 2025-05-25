<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

class Product {
    private $conn;

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo conectar a la base de datos.");
        }
    }

    public function getAll() {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.fecha_caducidad, p.lote, p.stock, pr.nombre AS proveedor 
                FROM Productos p 
                LEFT JOIN Proveedores pr ON p.id_proveedor = pr.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de productos.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.fecha_caducidad, p.lote, p.stock, p.id_proveedor, pr.nombre AS proveedor 
                FROM Productos p 
                LEFT JOIN Proveedores pr ON p.id_proveedor = pr.id_proveedor 
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
        $sql = "SELECT COUNT(*) as count FROM Productos WHERE nombre = ?";
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
        $sql = "SELECT COUNT(*) as count FROM MovimientosStock WHERE id_producto = ?";
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
            $sql = "INSERT INTO Productos (nombre, descripcion, precio, fecha_caducidad, lote, stock, id_proveedor) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param(
                "ssdssii",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['fecha_caducidad'],
                $data['lote'],
                $data['stock'],
                $data['id_proveedor']
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
            $sql = "UPDATE Productos SET nombre = ?, descripcion = ?, precio = ?, fecha_caducidad = ?, lote = ?, stock = ?, id_proveedor = ? 
                    WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param(
                "ssdssiii",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['fecha_caducidad'],
                $data['lote'],
                $data['stock'],
                $data['id_proveedor'],
                $id
            );
            $result = $stmt->execute();
            if (!$result || $stmt->affected_rows === 0) {
                throw new Exception("E001 Productos: No se actualizó ningún producto con ID $id.");
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
            $sql = "DELETE FROM Productos WHERE id_producto = ?";
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
        $sql = "SELECT id_proveedor, nombre FROM Proveedores";
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