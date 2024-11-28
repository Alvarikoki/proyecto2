<?php
session_start(); // Inicia la sesión

$facturas = file_get_contents('http://localhost/proyecto2/apis/api_facturas.php');
$facturas = json_decode($facturas, true);
$clientes = file_get_contents('http://localhost/proyecto2/apis/api_clientes.php');
$clientes = json_decode($clientes, true);
$productos = file_get_contents('http://localhost/proyecto2/apis/api_productos.php');
$productos = json_decode($productos, true);

function obtenerNombreCliente($id_cliente, $clientes) {
    foreach ($clientes as $cliente) {
        if ($cliente['id'] == $id_cliente) {
            return $cliente['nombre'];
        }
    }
    return 'Desconocido';
}

    function obtenerNombreProducto($id_producto, $productos) {
    foreach ($productos as $producto) {
        if ($producto['id'] == $id_producto) {
            return $producto['nombre'];
        }
    }
    return 'Desconocido';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $id_cliente = $_POST['id_cliente'] ?? null;
    $id_producto = $_POST['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $total = $_POST['total'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if ($accion === 'editar' && $id && $id_cliente && $id_producto && $cantidad && $total) {

        $data = [
            'id' => $id,
            'id_cliente' => $id_cliente,
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'total' => $total
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_facturas.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if ($statusCode == 200 && isset($responseData['message'])) {
            $_SESSION['message'] = "Factura actualizada exitosamente";
        } else {
            $_SESSION['error'] = "Error al actualizar la factura: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_factura.php');
        exit();

    } elseif ($accion === 'eliminar' && $id) {

        $data = ['id' => $id];

        $ch = curl_init('http://localhost/proyecto2/apis/api_facturas.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($statusCode == 200 && isset($responseData['message'])) {
            $_SESSION['message'] = "Factura eliminada exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la factura: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_factura.php');
        exit();

    } elseif ($accion === 'crear' && $id_cliente && $id_producto && $cantidad && $total) {
        $data = [
            'id_cliente' => $id_cliente,
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'total' => $total
        ];

        $ch = curl_init('http://localhost/proyecto2/apis/api_facturas.php');
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
            $_SESSION['error'] = "Error al crear la factura: " . ($responseData['error'] ?? 'Desconocido');
        }

        header('Location: form_factura.php');
        exit();
    }


}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Factura</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<div class="back-button">
    <a href="index.html">
        <img src="../img/row-icon.png" alt="Volver" class="icon">
    </a>
</div>

<h2>Formulario de creación de facturas</h2>

<!-- Mostrar mensaje de éxito o error -->
<?php if (isset($_SESSION['message'])): ?>
    <p style="color: green;"><?php echo $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="POST" action="form_factura.php">
    <input type="number" id="id" name="id" placeholder="" readonly hidden><br><br>

    <label for="id_cliente">Cliente:</label>
    <select id="id_cliente" name="id_cliente" required>
        <option value="">Seleccione un cliente</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="id_producto">Producto:</label>
    <select id="id_producto" name="id_producto" required>
    <option value="" data-precio="0">Seleccione un producto</option>
    <?php foreach ($productos as $producto): ?>
        <option value="<?php echo $producto['id']; ?>" data-precio="<?php echo $producto['precio']; ?>">
            <?php echo $producto['nombre']; ?>
        </option>
    <?php endforeach; ?>
    </select><br><br>


    <label for="cantidad">Cantidad:</label>
    <input type="number" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad" required min="1"><br><br>

    <label for="total">Total:</label>
    <input type="number" id="total" name="total" placeholder="Total de la factura" readonly><br><br>

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
        <th>Cliente</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Total</th>
    </tr>
    <?php foreach ($facturas as $factura): ?>
    <tr onclick="seleccionarFactura(
        <?php echo $factura['id_factura']; ?>, 
        <?php echo $factura['id_cliente']; ?>, 
        <?php echo $factura['id_producto']; ?>, 
        <?php echo $factura['cantidad']; ?>, 
        <?php echo $factura['total']; ?>
    )">
        <td><?php echo $factura['id_factura']; ?></td>
        <td><?php echo obtenerNombreCliente($factura['id_cliente'], $clientes); ?></td>
        <td><?php echo obtenerNombreProducto($factura['id_producto'], $productos); ?></td>
        <td><?php echo $factura['cantidad']; ?></td>
        <td><?php echo number_format($factura['total'], 2); ?></td>
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
    
    function seleccionarFactura(id, id_cliente, id_producto, cantidad, total) {
        document.getElementById('id').value = id;
        document.getElementById('id_cliente').value = id_cliente;
        document.getElementById('id_producto').value = id_producto;
        document.getElementById('cantidad').value = cantidad;
        document.getElementById('total').value = total;

        document.getElementById('upd').disabled = false;
        document.getElementById('del').disabled = false;
        document.getElementById('add').disabled = true;
    }

    function limpiarFormulario() {
        document.getElementById('id').value = "";
        document.getElementById('id_cliente').value = null;
        document.getElementById('id_producto').value = null;
        document.getElementById('cantidad').value = "";
        document.getElementById('total').value = "";

            
        document.getElementById('upd').disabled = true;
        document.getElementById('del').disabled = true;
        document.getElementById('add').disabled = false;
    }

    const selectProducto = document.getElementById('id_producto');
    const inputCantidad = document.getElementById('cantidad');
    const inputTotal = document.getElementById('total');

    function calcularTotal() {
        // Obtener el precio del producto seleccionado
        const precio = parseFloat(selectProducto.selectedOptions[0].getAttribute('data-precio')) || 0;
        const cantidad = parseInt(inputCantidad.value) || 0;

        // Calcular el total
        const total = precio * cantidad;

        // Actualizar el campo "Total"
        inputTotal.value = total.toFixed(2); // Mostrar con dos decimales
    }

    // Eventos para actualizar el total dinámicamente
    selectProducto.addEventListener('change', calcularTotal);
    inputCantidad.addEventListener('input', calcularTotal);
</script>

</body>
</html>
