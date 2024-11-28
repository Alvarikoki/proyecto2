<?php
header('Content-Type: application/json');
require_once '../db/productos.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'] ?? null;
        $precio = $data['precio'] ?? null;
        $cantidad = $data['cantidad'] ?? null;
    
        if (!$nombre || !$precio || !$cantidad) {
            echo json_encode(['error' => 'Todos los campos son obligatorios']);
        } else {
            $result = crearProducto($nombre, $precio, $cantidad);
            $response = $result === true ? 
                ['message' => 'Producto creado exitosamente'] : 
                ['error' => $result];
            echo json_encode($response);
        }
        break;
    

    case 'GET':
        $result = obtenerProductos();
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode($productos);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $precio = $data['precio'] ?? null;
        $cantidad = $data['cantidad'] ?? null;

        if (actualizarProducto($id, $nombre, $precio, $cantidad)) {
            echo json_encode(['message' => 'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['error' => 'Error al actualizar el producto']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        
        if ($id === null) {
            echo json_encode(['error' => 'El ID es obligatorio para eliminar el producto']);
            exit();
        }

        if (eliminarProducto($id)) {
            echo json_encode(['message' => 'Producto eliminado exitosamente']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el producto']);
        }
        break;

    default:
        echo json_encode(['error' => 'Método no soportado']);
        break;
}
?>
