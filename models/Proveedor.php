<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

class Proveedor {
    private $conn;

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            throw new Exception("E005 Base de Datos: No se pudo conectar a la base de datos.");
        }
    }

    public function getAll() {
        $sql = "SELECT id_proveedor, nombre, contacto, email FROM Proveedores";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de proveedores.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT id_proveedor, nombre, contacto, email FROM Proveedores WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$result) {
            throw new Exception("E002 Proveedores: Proveedor no encontrado.");
        }
        return $result;
    }

    public function create($data) {
        $sql = "INSERT INTO Proveedores (nombre, contacto, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("sss", $data['nombre'], $data['contacto'], $data['email']);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al crear el proveedor.");
        }
        $stmt->close();
        return true;
    }

    public function update($id, $data) {
        $sql = "UPDATE Proveedores SET nombre = ?, contacto = ?, email = ? WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("sssi", $data['nombre'], $data['contacto'], $data['email'], $id);
        $result = $stmt->execute();
        if (!$result || $stmt->affected_rows === 0) {
            throw new Exception("E002 Proveedores: No se actualizó ningún proveedor con ID $id.");
        }
        $stmt->close();
        return true;
    }

    public function delete($id) {
        $sql = "SELECT COUNT(*) as count FROM Productos WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result['count'] > 0) {
            throw new Exception("E002 Proveedores: No se puede eliminar el proveedor porque está ligado a productos.");
        }

        $sql = "DELETE FROM Proveedores WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if (!$result || $stmt->affected_rows === 0) {
            throw new Exception("E002 Proveedores: No se eliminó ningún proveedor con ID $id.");
        }
        $stmt->close();
        return true;
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>