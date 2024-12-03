<?php
header('Content-Type: application/json');
require_once '../db/productos.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (isset($data['nombre'], $data['precio'], $data['cantidad'])) {
            $nombre = $data['nombre'];
            $precio = $data['precio'];
            $cantidad = $data['cantidad'];
    
            if (!$nombre || !$precio || !$cantidad) {
                echo json_encode(['error' => 'Todos los campos son obligatorios']);
            } else {
                $result = crearProducto($nombre, $precio, $cantidad);
                $response = $result === true ? 
                    ['message' => 'Producto creado exitosamente'] : 
                    ['error' => $result];
                echo json_encode($response);
            }
        }elseif (isset($data['id'], $data['cantidad'])) {
            $id = $data['id'];
            $cantidad = $data['cantidad'];
    
            if (!$id || !$cantidad) {
                echo json_encode(['error' => 'ID y cantidad son obligatorios']);
            } else {
                $result2 = actualizarCantidad($id, $cantidad);
                $response2 = $result2 === true ? 
                    ['message' => 'Cantidad actualizada exitosamente'] : 
                    ['error' => $result2];
                echo json_encode($response2);
            }
        } else {
            echo json_encode(['error' => 'Datos inválidos']);
        }
        break;
    
    

        case 'GET':
            $result = obtenerProductos();
            $productos = [];
        
            // Recorrer los resultados
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        
            // Inspeccionar lo que devuelve la API
            var_dump($productos); 
            exit;
        
            // Respuesta en formato JSON (se ejecutará solo si eliminas el var_dump y exit)
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
