<?php
require_once 'conexion.php';

function crearCliente($nombre, $telefono, $correo) {
    global $conn;
    $sql = "INSERT INTO clientes (nombre, telefono, correo) VALUES ('$nombre', '$telefono', '$correo')";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

function obtenerCliente() {
    global $conn;
    $sql = "SELECT * FROM clientes";
    return $conn->query($sql);
}

function actualizarCliente($id, $nombre, $telefono, $correo) {
    global $conn;
    $sql = "UPDATE clientes SET nombre = '$nombre', telefono = '$telefono', correo = '$correo' WHERE id = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}

function eliminarCliente($id) {
    global $conn;
    $sql = "DELETE FROM clientes WHERE id = $id";
    if ($conn->query($sql)) {
        return true;
    } else {
        return $conn->error;
    }
}
?>