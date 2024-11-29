<?php
session_start(); // Inicia la sesión

$clientes = file_get_contents('http://localhost/proyecto2/apis/api_clientes.php');
$clientes = json_decode($clientes, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $correo = $_POST['correo'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if ($accion === 'editar' && $id && $nombre && $telefono && $correo) {

        $data = [
            'id' => $id,
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_clientes.php');
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
            $_SESSION['message'] = "Cliente actualizado exitosamente";
        } else {
            $_SESSION['error'] = "Error al actualizar el cliente: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_cliente.php');
        exit();
    } elseif ($accion === 'eliminar' && $id) {

        $data = ['id' => $id];

        $ch = curl_init('http://localhost/proyecto2/apis/api_clientes.php');
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
            $_SESSION['message'] = "Cliente eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el cliente: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_cliente.php');
        exit();
    } elseif ($accion === 'crear' && $nombre && $telefono && $correo) {
        $data = [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_clientes.php');
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
            $_SESSION['error'] = "Error al crear el cliente: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_cliente.php');
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

    <h2>Formulario de creación de clientes</h2>

    <!-- Mostrar mensaje de éxito o error -->
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo 
        ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); // Limpiar el mensaje de error después de mostrarlo 
        ?>
    <?php endif; ?>

    <form method="POST" action="form_cliente.php">
        <input type="number" id="id" name="id" placeholder="" readonly hidden><br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del cliente" required><br><br>

        <label for="precio">Telefono:</label>
        <input type="text" id="telefono" name="telefono" placeholder="Ingrese el telefono del cliente" required><br><br>

        <label for="cantidad">Correo:</label>
        <input type="email" id="correo" name="correo" placeholder="Ingrese el correo del cliente" required><br><br>

        <!-- Botones para agregar, editar o eliminar -->
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
            <th>Teléfono</th>
            <th>Correo</th>
        </tr>
        <?php foreach ($clientes as $producto): ?>
            <tr onclick="seleccionarCliente(<?php echo $producto['id']; ?>, '<?php echo addslashes($producto['nombre']); ?>', <?php echo $producto['telefono']; ?>, '<?php echo addslashes($producto['correo']); ?>')">
                <td><?php echo $producto['id']; ?></td>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['telefono']; ?></td>
                <td><?php echo $producto['correo']; ?></td>
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

        function seleccionarCliente(id, nombre, telefono, correo) {
            document.getElementById('id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('telefono').value = telefono;
            document.getElementById('correo').value = correo;

            document.getElementById('upd').disabled = false;
            document.getElementById('del').disabled = false;
            document.getElementById('add').disabled = true;
        }

        function limpiarFormulario() {
            // Vaciar el formulario
            document.getElementById('id').value = '';
            document.getElementById('nombre').value = '';
            document.getElementById('telefono').value = '';
            document.getElementById('correo').value = '';

            // Deshabilitar botones de editar y eliminar
            document.getElementById('upd').disabled = true;
            document.getElementById('del').disabled = true;
            document.getElementById('add').disabled = false;
        }
    </script>

</body>

</html>