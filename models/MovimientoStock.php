<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

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

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            // Verificar stock para salidas
            if ($data['tipo_movimiento'] === 'salida') {
                $sql = "SELECT stock FROM Productos WHERE id_producto = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $data['id_producto']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($result['stock'] < $data['cantidad']) {
                    throw new Exception("No hay suficiente stock para realizar la salida. Stock actual: " . $result['stock']);
                }
            }

            // Insertar movimiento
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