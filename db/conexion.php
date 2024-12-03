<?php
$servidor = 'mercado.mysql.database.azure.com';
$usuario = 'adminuser@mercado';
$contraseña = 'Admin123!';
$bd = 'facturacion';

$conn = mysqli_connect($servidor, $usuario, $contraseña, $bd);

if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");
?>
