<?php
$servidor = 'sql308.infinityfree.com';
$usuario = 'if0_37816829';
$contraseña = 'IM9hSv2RXVMJ6Jb';
$bd = 'if0_37816829_facturacion';

$conn = mysqli_connect($servidor, $usuario, $contraseña, $bd);

if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}
?>
