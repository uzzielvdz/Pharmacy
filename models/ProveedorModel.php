<?php
require_once dirname(__DIR__) . '/config/database.php';

class ProveedorModel {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM proveedores ORDER BY nombre";
        $result = $this->conn->query($query);
        $proveedores = [];
        while ($row = $result->fetch_assoc()) {
            $proveedores[] = $row;
        }
        return $proveedores;
    }

    public function getById($id) {
        $query = "SELECT * FROM proveedores WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $query = "INSERT INTO proveedores (nombre, tipo, contacto, telefono, email, estado, direccion, notas) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssss", 
            $data['nombre'],
            $data['tipo'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['estado'],
            $data['direccion'],
            $data['notas']
        );
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE proveedores 
                 SET nombre = ?, 
                     tipo = ?, 
                     contacto = ?, 
                     telefono = ?, 
                     email = ?, 
                     estado = ?, 
                     direccion = ?, 
                     notas = ? 
                 WHERE id_proveedor = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssssi", 
            $data['nombre'],
            $data['tipo'],
            $data['contacto'],
            $data['telefono'],
            $data['email'],
            $data['estado'],
            $data['direccion'],
            $data['notas'],
            $id
        );
        
        return $stmt->execute();
    }

    public function delete($id) {
        $this->conn->begin_transaction();
        try {
            $query = "DELETE FROM proveedores WHERE id_proveedor = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            
            if (!$result || $stmt->affected_rows === 0) {
                throw new Exception("No se pudo eliminar el proveedor.");
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getProductosByProveedor($id_proveedor) {
        $query = "SELECT id_producto FROM productos WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_proveedor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrdenesByProveedor($id_proveedor) {
        $query = "SELECT id_orden FROM ordenes_compra WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_proveedor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
} 