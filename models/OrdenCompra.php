<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/MovimientoStock.php';

class OrdenCompra {
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
        $sql = "SELECT oc.id_orden, oc.fecha_orden, oc.estado, oc.total, p.nombre AS nombre_proveedor 
                FROM OrdenesCompra oc 
                JOIN Proveedores p ON oc.id_proveedor = p.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: No se pudo obtener la lista de órdenes.");
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
                throw new Exception("E005 Base de Datos: Error al preparar la consulta para crear la orden.");
            }
            $stmt->bind_param("isd", $data['id_proveedor'], $data['fecha_orden'], $data['total']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: No se pudo crear la orden.");
            }
            $id_orden = $this->conn->insert_id;
            $stmt->close();

            foreach ($data['detalles'] as $detalle) {
                $sql = "INSERT INTO DetallesOrdenCompra (id_orden, id_producto, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("E005 Base de Datos: Error al preparar la consulta para los detalles.");
                }
                $stmt->bind_param("iiid", $id_orden, $detalle['id_producto'], $detalle['cantidad'], $detalle['precio_unitario']);
                $result = $stmt->execute();
                if (!$result) {
                    throw new Exception("E005 Base de Datos: No se pudo guardar el detalle de la orden.");
                }
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
                throw new Exception("E004 Órdenes: La orden no existe.");
            }
            if ($result['estado'] !== 'pendiente') {
                throw new Exception("E004 Órdenes: La orden no está en estado pendiente.");
            }

            $sql = "SELECT doc.id_producto, doc.cantidad 
                    FROM DetallesOrdenCompra doc 
                    WHERE doc.id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $detalles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            $movimientoStock = new MovimientoStock();
            foreach ($detalles as $detalle) {
                $movimientoData = [
                    'id_producto' => $detalle['id_producto'],
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $detalle['cantidad'],
                    'fecha' => date('Y-m-d H:i:s'),
                    'motivo' => 'Orden de compra #' . $id
                ];
                $movimientoStock->create($movimientoData);
            }

            $sql = "UPDATE OrdenesCompra SET estado = 'completada' WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: No se pudo completar la orden.");
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function cancelar($id) {
        $sql = "UPDATE OrdenesCompra SET estado = 'cancelada' WHERE id_orden = ? AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("E005 Base de Datos: No se pudo cancelar la orden.");
        }
        if ($stmt->affected_rows === 0) {
            throw new Exception("E004 Órdenes: La orden no está en estado pendiente o no existe.");
        }
        $stmt->close();
        return true;
    }

    public function delete($id) {
        $this->conn->begin_transaction();
        try {
            $sql = "SELECT COUNT(*) AS count 
                    FROM MovimientosStock m 
                    JOIN DetallesOrdenCompra doc ON m.id_producto = doc.id_producto 
                    WHERE doc.id_orden = ? AND m.motivo LIKE ?";
            $motivo = "Orden de compra #$id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $id, $motivo);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($result['count'] > 0) {
                throw new Exception("E004 Órdenes: No se puede eliminar la orden porque tiene movimientos de stock asociados.");
            }

            $sql = "DELETE FROM DetallesOrdenCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: No se pudo eliminar los detalles de la orden.");
            }
            $stmt->close();

            $sql = "DELETE FROM OrdenesCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: No se pudo eliminar la orden.");
            }
            if ($stmt->affected_rows === 0) {
                throw new Exception("E004 Órdenes: La orden no existe.");
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
            throw new Exception("E005 Base de Datos: No se pudo obtener la lista de proveedores.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductos() {
        $sql = "SELECT id_producto, nombre FROM Productos";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: No se pudo obtener la lista de productos.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrdenesPendientes() {
        $sql = "SELECT o.id_orden, p.nombre as proveedor, o.fecha_orden, o.total
                FROM ordenescompra o
                JOIN proveedores p ON o.id_proveedor = p.id_proveedor
                WHERE o.estado = 'pendiente'
                ORDER BY o.fecha_orden ASC
                LIMIT 5";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener órdenes pendientes.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>