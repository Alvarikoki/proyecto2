<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db/facturas.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Crear una nueva factura
        $data = json_decode(file_get_contents("php://input"), true);
        $id_cliente = $data['id_cliente'] ?? null;
        $id_producto = $data['id_producto'] ?? null;
        $cantidad = $data['cantidad'] ?? null;
        $total = $data['total'] ?? null;
    
        // Validación de campos
        if (!$id_cliente || !$id_producto || !$cantidad || !$total) {
            echo json_encode(['error' => 'Todos los campos son obligatorios']);
        } else {
            // Crear factura
            $result = crearFactura($id_cliente, $id_producto, $cantidad, $total);
            $response = $result === true ? 
                ['message' => 'Factura creada exitosamente'] : 
                ['error' => $result];
            echo json_encode($response);
        }
        break;

    case 'GET':
        // Obtener todas las facturas
        $result = obtenerFacturas();
        $facturas = [];
        while ($row = $result->fetch_assoc()) {
            $facturas[] = $row;
        }
        echo json_encode($facturas);
        break;

    case 'PUT':
        // Actualizar una factura existente
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        $id_cliente = $data['id_cliente'] ?? null;
        $id_producto = $data['id_producto'] ?? null;
        $cantidad = $data['cantidad'] ?? null;
        $total = $data['total'] ?? null;

        // Validación
        if (!$id || !$id_cliente || !$id_producto || !$cantidad || !$total) {
            echo json_encode(['error' => 'Todos los campos son obligatorios']);
        } else {
            if (actualizarFactura($id, $id_cliente, $id_producto, $cantidad, $total)) {
                echo json_encode(['message' => 'Factura actualizada exitosamente']);
            } else {
                echo json_encode(['error' => 'Error al actualizar la factura']);
            }
        }
        break;

    case 'DELETE':
        // Eliminar una factura
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        
        if ($id === null) {
            echo json_encode(['error' => 'El ID es obligatorio para eliminar la factura']);
            exit();
        }

        if (eliminarFactura($id)) {
            echo json_encode(['message' => 'Factura eliminada exitosamente']);
        } else {
            echo json_encode(['error' => 'Error al eliminar la factura']);
        }
        break;

    default:
        echo json_encode(['error' => 'Método no soportado']);
        break;
}
?>
