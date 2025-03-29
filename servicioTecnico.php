<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "celulares";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombre_cliente = $_POST['nombre_cliente'];
$telefono_cliente = $_POST['telefono_cliente'];
$modelo_telefono = $_POST['modelo_telefono'];
$imei_telefono = $_POST['imei_telefono'];
$descripcion_problema = $_POST['descripcion_problema'];
$fecha_recepcion = $_POST['fecha_recepcion'];
$costo_estimado = $_POST['costo_estimado'];



// Insertar los datos en la base de datos
$sql = "SELECT nombre_cliente, telefono_cliente, modelo_telefono, imei, descripcion_problema, fecha_recepcion, costo_estimado, numero_seguimiento from ordenes_trabajo
        VALUES ('$nombre_cliente', '$telefono_cliente', '$modelo_telefono', '$imei_telefono','$descripcion_problema', '$fecha_recepcion', '$costo_estimado', '$numero_seguimiento')";


// Actualizar estado de reparación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_estado'])) {
    $id_reparacion = $_POST['id_reparacion'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    $sql = "UPDATE reparaciones SET estado='$nuevo_estado' WHERE id='$id_reparacion'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Estado actualizado exitosamente.');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

//seguimiento clientes
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $numero_seguimiento = $_POST['numero_seguimiento'];

    // Ajustar consulta para buscar por número de seguimiento
    $sql = "SELECT nombre_cliente, telefono_cliente, modelo_telefono, imei, descripcion_problema, fecha_recepcion, costo_estimado,numero_seguimiento FROM ordenes_trabajo WHERE numero_seguimiento = '$numero_seguimiento' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cliente = $row['nombre_cliente'];
            $telefono = $row['telefono_cliente'];
            $modelo = $row['modelo_telefono'];
            $imei_telefono = $row['imei'];
            $descripcion = $row['descripcion_problema'];
            $fechaRecepcion = $row['fecha_recepcion'];
            $costo = $row['costo_estimado'];
            $estado = "Reparación encontrada"; // Cambia el estado a un mensaje positivo
        } else {
            $estado = "No se encontraron reparaciones para este número de seguimiento.";
        }
    } else {
        $estado = "Error en la consulta: " . $conn->error;
    }
}


// Registrar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_venta'])) {
    $producto = $_POST['producto'];
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];
    
    // Verificar stock antes de insertar
    $sql_stock = "SELECT stock FROM productos WHERE nombre='$producto'";
    $resultado_stock = $conn->query($sql_stock);
    $row_stock = $resultado_stock->fetch_assoc();
    
    if ($row_stock['stock'] > 0) {
        // Registrar venta
        $sql_venta = "INSERT INTO ventas (producto, monto, metodo_pago) VALUES ('$producto', '$monto', '$metodo_pago')";
        
        if ($conn->query($sql_venta) === TRUE) {
            // Actualizar stock
            $nuevo_stock = $row_stock['stock'] - 1;
            $sql_actualizar_stock = "UPDATE productos SET stock='$nuevo_stock' WHERE nombre='$producto'";
            $conn->query($sql_actualizar_stock);
            
            echo "<script>alert('Venta registrada exitosamente.');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('No hay suficiente stock para realizar la venta.');</script>";
    }
}

// Generar reporte de ventas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reporte_ventas'])) {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    
    $sql_reporte = "SELECT SUM(monto) AS total_ventas FROM ventas WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    $resultado_reporte = $conn->query($sql_reporte);
    $row_reporte = $resultado_reporte->fetch_assoc();
    
    echo "<script>alert('Total vendido desde $fecha_inicio hasta $fecha_fin: " . $row_reporte['total_ventas'] . "');</script>";
}

// Limpiar tabla de ventas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['limpiar_ventas'])) {
    $sql_limpieza = "TRUNCATE TABLE ventas";
    
    if ($conn->query($sql_limpieza) === TRUE) {
        echo "<script>alert('Tabla de ventas limpiada exitosamente.');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>cd Sistemas 📱</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #000000, #6a0dad, #ff6600);
            color: white;
        }

        .container {
            width: 90%;
            max-width: 500px;
            padding: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(255, 102, 0, 0.5);
        }

        h1 {
            font-size: 2rem;
            color: #ff6600;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .square-button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            margin: 10px 0;
            border: 2px solid #ff6600;
            border-radius: 10px;
            background: #6a0dad;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 6px rgba(255, 102, 0, 0.3);
        }

        .square-button:hover {
            background: #ff6600;
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(255, 102, 0, 0.5);
        }

        .square-button:active {
            transform: scale(1);
            box-shadow: 0 4px 8px rgba(255, 102, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Servicio Técnico</h1>
        <div class="button-container">
            <button class="square-button" onclick="window.location.href='orden_trabajo.php'">📝 Orden de Trabajo</button>
            <button class="square-button" onclick="window.location.href='mostrar_seguimiento.php'">📋 Lista de Orden</button>
            <button class="square-button" onclick="window.location.href='actualizar_seguimiento.php'">✏️ Actualizar</button>
            <button class="square-button" onclick="window.location.href='eliminar_seguimiento.php'">❌ Eliminar</button>
        </div>
    </div>
</body>
</html>
