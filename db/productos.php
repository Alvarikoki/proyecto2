<?php
require_once 'conexion.php';

function crearProducto($nombre, $precio, $cantidad) {
    global $conn;
    $sql = "INSERT INTO productos (nombre, precio, cantidad) VALUES ('$nombre', '$precio', '$cantidad')";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

function obtenerProductos($id = null) {
    global $conn;
    if ($id) {
        $sql = "SELECT * FROM productos WHERE id = $id";
    } else {
        $sql = "SELECT * FROM productos";
    }
    return $conn->query($sql);
}

function actualizarProducto($id, $nombre, $precio, $cantidad) {
    global $conn;
    $sql = "UPDATE productos SET nombre = '$nombre', precio = '$precio', cantidad = '$cantidad' WHERE id = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

function eliminarProducto($id) {
    global $conn;
    $sql = "DELETE FROM productos WHERE id = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}
?>