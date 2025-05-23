<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/models/MovimientoStock.php';

class OrdenCompra {
    private $conn;
    private $movimientoModel;

    public function __construct() {
        try {
            $this->conn = getConnection();
            $this->movimientoModel = new MovimientoStock();
        } catch (Exception $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    public function getAll() {
        $sql = "SELECT oc.id_orden, oc.fecha_orden, oc.estado, oc.total, p.nombre AS proveedor 
                FROM OrdenesCompra oc 
                JOIN Proveedores p ON oc.id_proveedor = p.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO OrdenesCompra (id_proveedor, fecha_orden, estado, total) 
                    VALUES (?, ?, 'pendiente', ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conn->error);
            }
            $stmt->bind_param("isd", $data['id_proveedor'], $data['fecha_orden'], $data['total']);
            $stmt->execute();
            $id_orden = $this->conn->insert_id;
            $stmt->close();

            foreach ($data['detalles'] as $detalle) {
                $sql = "INSERT INTO DetallesOrdenCompra (id_orden, id_producto, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparando consulta: " . $this->conn->error);
                }
                $stmt->bind_param("iiid", $id_orden, $detalle['id_producto'], $detalle['cantidad'], $detalle['precio_unitario']);
                $stmt->execute();
                $stmt->close();
            }

            $this->conn->commit();
            return $id_orden;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function completar($id) {
        $this->conn->begin_transaction();
        try {
            $sql = "SELECT estado FROM OrdenesCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$result) {
                throw new Exception("Orden no encontrada.");
            }
            if ($result['estado'] !== 'pendiente') {
                throw new Exception("La orden no está pendiente.");
            }

            $sql = "SELECT id_producto, cantidad FROM DetallesOrdenCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $detalles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($detalles as $detalle) {
                $movimientoData = [
                    'id_producto' => $detalle['id_producto'],
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $detalle['cantidad'],
                    'fecha' => date('Y-m-d H:i:s'),
                    'motivo' => 'Orden de compra #' . $id
                ];
                $this->movimientoModel->create($movimientoData);
            }

            $sql = "UPDATE OrdenesCompra SET estado = 'completada' WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function cancelar($id) {
        $sql = "UPDATE OrdenesCompra SET estado = 'cancelada' WHERE id_orden = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function getProveedores() {
        $sql = "SELECT id_proveedor, nombre FROM Proveedores";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductos() {
        $sql = "SELECT id_producto, nombre FROM Productos";
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