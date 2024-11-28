<?php
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';
$bd = 'facturacion';

$conn = mysqli_connect($servidor, $usuario, $contraseña, $bd);

if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}
?>
