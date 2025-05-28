<?php
require_once dirname(__DIR__) . '/config/config.php';

class MovimientoStock {
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
        $sql = "SELECT m.id_movimiento, m.id_producto, p.nombre AS producto, m.tipo_movimiento, m.cantidad, m.fecha, m.motivo 
                FROM MovimientosStock m 
                JOIN Productos p ON m.id_producto = p.id_producto";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de movimientos.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT m.*, p.nombre AS producto 
                FROM MovimientosStock m 
                JOIN Productos p ON m.id_producto = p.id_producto 
                WHERE m.id_movimiento = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$result) {
            throw new Exception("E003 Movimientos: Movimiento no encontrado.");
        }
        return $result;
    }

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            $sql = "SELECT stock, fecha_caducidad FROM Productos WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("i", $data['id_producto']);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$product) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
            if ($data['tipo_movimiento'] === 'salida') {
                if ($product['stock'] < $data['cantidad']) {
                    throw new Exception("E003 Movimientos: No hay suficiente stock para realizar la salida. Stock actual: " . $product['stock'] . ".");
                }
                if ($data['motivo'] !== 'Eliminación de producto' && $product['fecha_caducidad'] && $product['fecha_caducidad'] < date('Y-m-d')) {
                    throw new Exception("E003 Movimientos: No se puede registrar salida de un producto caducado.");
                }
            }

            $sql = "INSERT INTO MovimientosStock (id_producto, tipo_movimiento, cantidad, fecha, motivo) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param(
                "isiss",
                $data['id_producto'],
                $data['tipo_movimiento'],
                $data['cantidad'],
                $data['fecha'],
                $data['motivo']
            );
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al registrar el movimiento.");
            }
            $stmt->close();

            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stockChange = $data['tipo_movimiento'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("ii", $stockChange, $data['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al actualizar el stock.");
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->conn->begin_transaction();
        try {
            $current = $this->getById($id);
            $currentStockChange = $current['tipo_movimiento'] === 'entrada' ? $current['cantidad'] : -$current['cantidad'];

            $sql = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("ii", $currentStockChange, $current['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al revertir el stock.");
            }
            $stmt->close();

            $sql = "SELECT stock, fecha_caducidad FROM Productos WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("i", $data['id_producto']);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$product) {
                throw new Exception("E001 Productos: Producto no encontrado.");
            }
            if ($data['tipo_movimiento'] === 'salida') {
                if ($product['stock'] < $data['cantidad']) {
                    throw new Exception("E003 Movimientos: No hay suficiente stock para realizar la salida. Stock actual: " . $product['stock'] . ".");
                }
                if ($data['motivo'] !== 'Eliminación de producto' && $product['fecha_caducidad'] && $product['fecha_caducidad'] < date('Y-m-d')) {
                    throw new Exception("E003 Movimientos: No se puede registrar salida de un producto caducado.");
                }
            }

            $sql = "UPDATE MovimientosStock SET id_producto = ?, tipo_movimiento = ?, cantidad = ?, fecha = ?, motivo = ? 
                    WHERE id_movimiento = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param(
                "isisis",
                $data['id_producto'],
                $data['tipo_movimiento'],
                $data['cantidad'],
                $data['fecha'],
                $data['motivo'],
                $id
            );
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al actualizar el movimiento.");
            }
            $stmt->close();

            $newStockChange = $data['tipo_movimiento'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("ii", $newStockChange, $data['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al actualizar el stock.");
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
            $current = $this->getById($id);
            $stockChange = $current['tipo_movimiento'] === 'entrada' ? -$current['cantidad'] : $current['cantidad'];

            if ($stockChange < 0) {
                $sql = "SELECT stock FROM Productos WHERE id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
                }
                $stmt->bind_param("i", $current['id_producto']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($result['stock'] < abs($stockChange)) {
                    throw new Exception("E003 Movimientos: No hay suficiente stock para revertir la eliminación. Stock actual: " . $result['stock'] . ".");
                }
            }

            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("ii", $stockChange, $current['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al revertir el stock.");
            }
            $stmt->close();

            $sql = "DELETE FROM MovimientosStock WHERE id_movimiento = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("E005 Base de Datos: Error al preparar la consulta.");
            }
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("E005 Base de Datos: Error al eliminar el movimiento.");
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function getProductos() {
        $sql = "SELECT id_producto, nombre FROM Productos";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("E005 Base de Datos: Error al obtener la lista de productos.");
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>