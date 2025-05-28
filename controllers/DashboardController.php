<?php
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__) . '/models/MovimientoStock.php';
require_once dirname(__DIR__) . '/models/OrdenCompra.php';

class DashboardController {
    private $productoModel;
    private $movimientoModel;
    private $ordenModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
        $this->movimientoModel = new MovimientoStock();
        $this->ordenModel = new OrdenCompra();
    }

    public function index() {
        // Inicializar variables con valores por defecto
        $productosBajoStock = [];
        $productosProximosCaducar = [];
        $movimientosRecientes = [];
        $ordenesPendientes = [];
        $stats = [
            'total_productos' => 0,
            'productos_bajo_stock' => 0,
            'productos_caducados' => 0,
            'ordenes_pendientes' => 0
        ];
        $errors = [];

        try {
            // Productos con bajo stock
            $productosBajoStock = $this->productoModel->getProductosBajoStock();
            if (empty($productosBajoStock)) {
                $errors[] = "No se encontraron productos con bajo stock";
            }
            
            // Productos próximos a caducar
            $productosProximosCaducar = $this->productoModel->getProductosProximosCaducar();
            if (empty($productosProximosCaducar)) {
                $errors[] = "No se encontraron productos próximos a caducar";
            }
            
            // Movimientos recientes
            $movimientosRecientes = $this->movimientoModel->getMovimientosRecientes();
            if (empty($movimientosRecientes)) {
                $errors[] = "No se encontraron movimientos recientes";
            }
            
            // Órdenes pendientes
            $ordenesPendientes = $this->ordenModel->getOrdenesPendientes();
            if (empty($ordenesPendientes)) {
                $errors[] = "No se encontraron órdenes pendientes";
            }
            
            // Estadísticas generales
            $stats = [
                'total_productos' => $this->productoModel->getTotalProductos(),
                'productos_bajo_stock' => count($productosBajoStock),
                'productos_caducados' => $this->productoModel->getTotalProductosCaducados(),
                'ordenes_pendientes' => count($ordenesPendientes)
            ];

            // Debug: Mostrar los datos obtenidos
            error_log("Productos bajo stock: " . print_r($productosBajoStock, true));
            error_log("Productos próximos a caducar: " . print_r($productosProximosCaducar, true));
            error_log("Movimientos recientes: " . print_r($movimientosRecientes, true));
            error_log("Órdenes pendientes: " . print_r($ordenesPendientes, true));
            error_log("Estadísticas: " . print_r($stats, true));

        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            error_log("Error en DashboardController: " . $e->getMessage());
        }

        require_once VIEWS_PATH . '/dashboard/index.php';
    }
} 