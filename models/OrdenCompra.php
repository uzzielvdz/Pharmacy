<?php
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
if (!function_exists('getConnection')) {
    require_once ROOT_PATH . '/config/database.php';
}
require_once ROOT_PATH . '/models/MovimientoStock.php';

class OrdenCompra {
    private $conn;

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            die("No se pudo conectar a la base de datos: " . $e->getMessage());
        }
    }

    public function getAll() {
        $sql = "SELECT oc.id_orden, oc.fecha_orden, oc.estado, oc.total, p.nombre AS proveedor 
                FROM OrdenesCompra oc 
                JOIN Proveedores p ON oc.id_proveedor = p.id_proveedor";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("No se pudo obtener la lista de órdenes: " . $this->conn->error);
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
                throw new Exception("Error al preparar la consulta para crear la orden.");
            }
            $stmt->bind_param("isd", $data['id_proveedor'], $data['fecha_orden'], $data['total']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo crear la orden: " . $stmt->error);
            }
            $id_orden = $this->conn->insert_id;
            $stmt->close();

            foreach ($data['detalles'] as $detalle) {
                $sql = "INSERT INTO DetallesOrdenCompra (id_orden, id_producto, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta para los detalles.");
                }
                $stmt->bind_param("iiid", $id_orden, $detalle['id_producto'], $detalle['cantidad'], $detalle['precio_unitario']);
                $result = $stmt->execute();
                if (!$result) {
                    throw new Exception("No se pudo guardar el detalle: " . $stmt->error);
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
                throw new Exception("Orden no encontrada.");
            }
            if ($result['estado'] !== 'pendiente') {
                throw new Exception("La orden no está pendiente.");
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
                throw new Exception("No se pudo completar la orden: " . $stmt->error);
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
            throw new Exception("No se pudo cancelar la orden: " . $stmt->error);
        }
        if ($stmt->affected_rows === 0) {
            throw new Exception("La orden no está pendiente o no existe.");
        }
        $stmt->close();
        return true;
    }

    public function delete($id) {
        $this->conn->begin_transaction();
        try {
            // Verificar si la orden tiene movimientos asociados
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
                throw new Exception("No se puede eliminar la orden porque tiene movimientos de stock asociados.");
            }

            // Eliminar detalles
            $sql = "DELETE FROM DetallesOrdenCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo eliminar los detalles de la orden: " . $stmt->error);
            }
            $stmt->close();

            // Eliminar orden
            $sql = "DELETE FROM OrdenesCompra WHERE id_orden = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo eliminar la orden: " . $stmt->error);
            }
            if ($stmt->affected_rows === 0) {
                throw new Exception("La orden no existe.");
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
            throw new Exception("No se pudo obtener la lista de proveedores: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductos() {
        $sql = "SELECT id_producto, nombre FROM Productos";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("No se pudo obtener la lista de productos: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>