<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

class Product {
    private $conn;

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function getAll() {
        $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.fecha_caducidad, p.lote, p.stock, pr.nombre AS proveedor 
                FROM Productos p 
                LEFT JOIN Proveedores pr ON p.id_proveedor = pr.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
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
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function checkNameExists($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM Productos WHERE nombre = ?";
        if ($excludeId !== null) {
            $sql .= " AND id_producto != ?";
        }
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        if ($excludeId !== null) {
            $stmt->bind_param("si", $nombre, $excludeId);
        } else {
            $stmt->bind_param("s", $nombre);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] > 0;
    }

    public function checkHasMovements($id) {
        $sql = "SELECT COUNT(*) as count FROM MovimientosStock WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] > 0;
    }

    public function create($data) {
        if ($this->checkNameExists($data['nombre'])) {
            throw new Exception("Ya existe un producto con el nombre: " . $data['nombre']);
        }
        $sql = "INSERT INTO Productos (nombre, descripcion, precio, fecha_caducidad, lote, stock, id_proveedor) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
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
        $insertId = $this->conn->insert_id;
        $stmt->close();
        return $result ? $insertId : false;
    }

    public function update($id, $data) {
        if ($this->checkNameExists($data['nombre'], $id)) {
            throw new Exception("Ya existe otro producto con el nombre: " . $data['nombre']);
        }
        $sql = "UPDATE Productos SET nombre = ?, descripcion = ?, precio = ?, fecha_caducidad = ?, lote = ?, stock = ?, id_proveedor = ? 
                WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
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
        $stmt->close();
        return $result;
    }

    public function delete($id) {
        if ($this->checkHasMovements($id)) {
            throw new Exception("No se puede eliminar el producto porque tiene movimientos de stock asociados.");
        }
        $sql = "DELETE FROM Productos WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        if ($result && $affectedRows > 0) {
            return true;
        } else {
            throw new Exception("No se eliminó ningún producto con ID $id.");
        }
    }

    public function getProveedores() {
        $sql = "SELECT id_proveedor, nombre FROM Proveedores";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>