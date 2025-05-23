<?php
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
if (!function_exists('getConnection')) {
    require_once ROOT_PATH . '/config/database.php';
}

class MovimientoStock {
    private $conn;

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            die("No se pudo conectar a la base de datos: " . $e->getMessage());
        }
    }

    public function getAll() {
        $sql = "SELECT m.id_movimiento, m.id_producto, p.nombre AS producto, m.tipo_movimiento, m.cantidad, m.fecha, m.motivo 
                FROM MovimientosStock m 
                JOIN Productos p ON m.id_producto = p.id_producto";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("No se pudo obtener la lista de movimientos: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT m.*, p.nombre AS producto 
                FROM MovimientosStock m 
                JOIN Productos p ON m.id_producto = p.id_producto 
                WHERE m.id_movimiento = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$result) {
            throw new Exception("Movimiento no encontrado.");
        }
        return $result;
    }

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            // Validar stock y caducidad
            $sql = "SELECT stock, fecha_caducidad FROM Productos WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta para validar el producto.");
            }
            $stmt->bind_param("i", $data['id_producto']);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$product) {
                throw new Exception("Producto no encontrado.");
            }

            if ($data['tipo_movimiento'] === 'salida') {
                if ($product['stock'] < $data['cantidad']) {
                    throw new Exception("No hay suficiente stock para realizar la salida. Stock actual: " . $product['stock']);
                }
                // Omitir validación de caducidad para eliminaciones
                if ($data['motivo'] !== 'Eliminación de producto' && $product['fecha_caducidad'] && $product['fecha_caducidad'] < date('Y-m-d')) {
                    throw new Exception("No se puede registrar salida de un producto caducado");
                }
            }

            // Registrar movimiento
            $sql = "INSERT INTO MovimientosStock (id_producto, tipo_movimiento, cantidad, fecha, motivo) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta para registrar el movimiento.");
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
                throw new Exception("No se pudo registrar el movimiento: " . $stmt->error);
            }
            $stmt->close();

            // Actualizar stock
            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stockChange = $data['tipo_movimiento'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta para actualizar el stock.");
            }
            $stmt->bind_param("ii", $stockChange, $data['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo actualizar el stock: " . $stmt->error);
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
            // Obtener movimiento actual
            $current = $this->getById($id);
            $currentStockChange = $current['tipo_movimiento'] === 'entrada' ? $current['cantidad'] : -$current['cantidad'];

            // Revertir stock actual
            $sql = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $currentStockChange, $current['id_producto']);
            $stmt->execute();
            $stmt->close();

            // Validar nuevo stock y caducidad
            $sql = "SELECT stock, fecha_caducidad FROM Productos WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $data['id_producto']);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($data['tipo_movimiento'] === 'salida') {
                if ($product['stock'] < $data['cantidad']) {
                    throw new Exception("No hay suficiente stock para realizar la salida. Stock actual: " . $product['stock']);
                }
                if ($data['motivo'] !== 'Eliminación de producto' && $product['fecha_caducidad'] && $product['fecha_caducidad'] < date('Y-m-d')) {
                    throw new Exception("No se puede registrar salida de un producto caducado");
                }
            }

            // Actualizar movimiento
            $sql = "UPDATE MovimientosStock SET id_producto = ?, tipo_movimiento = ?, cantidad = ?, fecha = ?, motivo = ? 
                    WHERE id_movimiento = ?";
            $stmt = $this->conn->prepare($sql);
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
                throw new Exception("No se pudo actualizar el movimiento: " . $stmt->error);
            }
            $stmt->close();

            // Aplicar nuevo stock
            $newStockChange = $data['tipo_movimiento'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $newStockChange, $data['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo actualizar el stock: " . $stmt->error);
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

            // Verificar stock antes de revertir
            if ($stockChange < 0) {
                $sql = "SELECT stock FROM Productos WHERE id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $current['id_producto']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($result['stock'] < abs($stockChange)) {
                    throw new Exception("No hay suficiente stock para revertir la eliminación. Stock actual: " . $result['stock']);
                }
            }

            // Revertir stock
            $sql = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $stockChange, $current['id_producto']);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo actualizar el stock: " . $stmt->error);
            }
            $stmt->close();

            // Eliminar movimiento
            $sql = "DELETE FROM MovimientosStock WHERE id_movimiento = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("No se pudo eliminar el movimiento: " . $stmt->error);
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
            throw new Exception("No se pudo obtener la lista de productos: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>