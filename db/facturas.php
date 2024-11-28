<?php
require_once 'conexion.php';  // Asegúrate de que el archivo de conexión esté en el lugar correcto

// Función para crear una nueva factura
function crearFactura($id_cliente, $id_producto, $cantidad, $total) {
    global $conn;
    $sql = "INSERT INTO factura (id_cliente, id_producto, cantidad, total) 
            VALUES ('$id_cliente', '$id_producto', '$cantidad', '$total')";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

// Función para obtener todas las facturas
function obtenerFacturas() {
    global $conn;
    $sql = "SELECT * FROM factura";
    return $conn->query($sql);
}

// Función para actualizar una factura existente
function actualizarFactura($id, $id_cliente, $id_producto, $cantidad, $total) {
    global $conn;
    $sql = "UPDATE factura 
            SET id_cliente = '$id_cliente', id_producto = '$id_producto', cantidad = '$cantidad', total = '$total' 
            WHERE id_factura = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

// Función para eliminar una factura
function eliminarFactura($id) {
    global $conn;
    $sql = "DELETE FROM factura WHERE id_factura = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}
?>
