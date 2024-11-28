<?php
header('Content-Type: application/json');
require_once '../db/clientes.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'] ?? null;
        $telefono = $data['telefono'] ?? null;
        $correo = $data['correo'] ?? null;
    
        if (!$nombre || !$telefono || !$correo) {
            echo json_encode(['error' => 'Todos los campos son obligatorios']);
        } else {
            $result = crearCliente($nombre, $telefono, $correo);
            $response = $result === true ? 
                ['message' => 'Producto creado exitosamente'] : 
                ['error' => $result];
            echo json_encode($response);
        }
        break;
    

    case 'GET':
        $result = obtenerCliente();
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        echo json_encode($clientes);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $telefono = $data['telefono'] ?? null;
        $correo = $data['correo'] ?? null;

        if (actualizarCliente($id, $nombre, $telefono, $correo)) {
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

        if (eliminarCliente($id)) {
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
