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
             $stmt->bind_param("i", $id);
             $stmt->execute();
             return $stmt->get_result()->fetch_assoc();
         }

         public function create($data) {
             $sql = "INSERT INTO Productos (nombre, descripcion, precio, fecha_caducidad, lote, stock, id_proveedor) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
             $stmt = $this->conn->prepare($sql);
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
             return $stmt->execute();
         }

         public function update($id, $data) {
             $sql = "UPDATE Productos SET nombre = ?, descripcion = ?, precio = ?, fecha_caducidad = ?, lote = ?, stock = ?, id_proveedor = ? 
                     WHERE id_producto = ?";
             $stmt = $this->conn->prepare($sql);
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
             return $stmt->execute();
         }

         public function delete($id) {
             $sql = "DELETE FROM Productos WHERE id_producto = ?";
             $stmt = $this->conn->prepare($sql);
             $stmt->bind_param("i", $id);
             return $stmt->execute();
         }

         public function __destruct() {
             $this->conn->close();
         }
     }
     ?>