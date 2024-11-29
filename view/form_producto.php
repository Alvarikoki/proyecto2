<?php
session_start(); // Inicia la sesión

$productos = file_get_contents('http://localhost/proyecto2/apis/api_productos.php');
$productos = json_decode($productos, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if ($accion === 'editar' && $id && $nombre && $precio && $cantidad) {

        $data = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_productos.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // PUT para actualizar
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if ($statusCode == 200 && isset($responseData['message'])) {
            $_SESSION['message'] = "Producto actualizado exitosamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el producto: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_producto.php');
        exit();

    } elseif ($accion === 'eliminar' && $id) {

        $data = ['id' => $id];

        $ch = curl_init('http://localhost/proyecto2/apis/api_productos.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // DELETE para eliminar
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($statusCode == 200 && isset($responseData['message'])) {
            $_SESSION['message'] = "Producto eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el producto: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_producto.php');
        exit();

    } elseif ($accion === 'crear' && $nombre && $precio && $cantidad) {
        $data = [
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_productos.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if ($statusCode == 200 && isset($responseData['message'])) {
            $_SESSION['message'] = $responseData['message'];
        } else {
            $_SESSION['error'] = "Error al crear el producto: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_producto.php');
        exit();
    }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<div class="back-button">
    <a href="../index.html">
        <img src="../img/row-icon.png" alt="Volver" class="icon">
    </a>
</div>

<h2>Formulario de creación de productos</h2>

<!-- Mostrar mensaje de éxito o error -->
<?php if (isset($_SESSION['message'])): ?>
    <p style="color: green;"><?php echo $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo ?>
<?php endif; ?>

<form method="POST" action="form_producto.php">
    <input type="number" id="id" name="id" placeholder="" readonly hidden><br><br>

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del producto" required><br><br>

    <label for="precio">Precio:</label>
    <input type="number" id="precio" name="precio" step="0.01" placeholder="Ingrese el precio del producto" min="0" required><br><br>

    <label for="cantidad">Cantidad:</label>
    <input type="number" id="cantidad" name="cantidad" placeholder="Ingrese una cantidad" required min="1"><br><br>

    <div id="botones" class="botones">
            <button type="submit" name="accion" id="add" value="crear">Agregar</button>
            <button type="submit" name="accion" id="upd" value="editar">Editar</button>
            <button type="submit" name="accion" id="del" value="eliminar">Eliminar</button>
            <button type="button" id="limpiar" onclick="limpiarFormulario()">Limpiar</button>
    </div>
</form>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Cantidad</th>
    </tr>
    <?php foreach ($productos as $producto): ?>
        <tr onclick="seleccionarProducto(<?php echo $producto['id']; ?>, '<?php echo $producto['nombre']; ?>', <?php echo $producto['precio']; ?>, <?php echo $producto['cantidad']; ?>)">
            <td><?php echo $producto['id']; ?></td>
            <td><?php echo $producto['nombre']; ?></td>
            <td>₡<?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['cantidad']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function() {
            const btnEditar = document.getElementById('upd');
            const btnEliminar = document.getElementById('del');

            btnEditar.disabled = true;
            btnEliminar.disabled = true;
    });
    
    function seleccionarProducto(id, nombre, precio, cantidad) {
        document.getElementById('id').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('precio').value = precio;
        document.getElementById('cantidad').value = cantidad;

        document.getElementById('upd').disabled = false;
        document.getElementById('del').disabled = false;
        document.getElementById('add').disabled = true;
    }

    function limpiarFormulario() {
        document.getElementById('id').value = "";
        document.getElementById('nombre').value = "";
        document.getElementById('precio').value = "";
        document.getElementById('cantidad').value = "";

            
        document.getElementById('upd').disabled = true;
        document.getElementById('del').disabled = true;
        document.getElementById('add').disabled = false;
    }
</script>

</body>
</html>
