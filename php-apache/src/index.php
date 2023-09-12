<?php
// Función para eliminar un registro por clave
function eliminarRegistro($clave) {
    try {
        $url = "pgsql:host=172.17.0.3;port=5432;dbname=mydb;";
        $user = "postgres";
        $password = "pass";
        $pdo = new PDO($url, $user, $password);

        $query = "DELETE FROM mytable WHERE clave = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$clave]);

        $pdo = null;
    } catch (PDOException $e) {
        die('Error en la conexión a la base de datos: ' . $e->getMessage());
    }
}

$claveDefault = "";
$nombreDefault = "";
$direccionDefault = "";
$telefonoDefault = "";

if (isset($_GET['clave'])) {
    try {
        $url = "pgsql:host=172.17.0.3;port=5432;dbname=mydb;";
        $user = "postgres";
        $password = "pass";
        $pdo = new PDO($url, $user, $password);
        $claveDefault = $_GET['clave'];

        $query = "SELECT * FROM mytable WHERE clave = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$claveDefault]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            // Si se encontró un registro, establece los valores por defecto
            $nombreDefault = $fila['nombre'];
            $direccionDefault = $fila['direccion'];
            $telefonoDefault = $fila['telefono'];
                   
        }
        $pdo = null;
    } catch (PDOException $e) {
        die('Error en la conexión a la base de datos: ' . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $url = "pgsql:host=172.17.0.3;port=5432;dbname=mydb;";
        $user = "postgres";
        $password = "pass";
        $pdo = new PDO($url, $user, $password);

        $clave = $_POST['clave'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];

        if (!empty($claveDefault)) {
            // Actualizar registro existente si $claveDefault no está vacío
            $query = "UPDATE mytable SET nombre = ?, direccion = ?, telefono = ? WHERE clave = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nombre, $direccion, $telefono, $claveDefault]);
        } else {
            // Crear nuevo registro si $claveDefault está vacío
            $query = "INSERT INTO mytable (nombre, direccion, telefono) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nombre, $direccion, $telefono]);
        }
        $pdo = null;

        // Redireccionar a la página principal después de realizar la operación
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } catch (PDOException $e) {
        die('Error en la conexión a la base de datos: ' . $e->getMessage());
    }
}

// Agrega el manejo de la solicitud para borrar un registro
if (isset($_GET['borrar'])) {
    eliminarRegistro($_GET['borrar']);
    // Redireccionar a la página principal después de eliminar el registro
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}
?>
<!-- -----------FORMULARIO----------- -->
<html>
<head>
    <title>Formulario</title>
</head>
<body>

<h2>Formulario</h2>
<div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="clave" id="clave" value="<?php echo $claveDefault; ?>">
        <table cellpadding="5">
            <tr>
                <td>Nombre</td> <td><input type="text" name="nombre" id="nombre" value="<?php echo $nombreDefault; ?>" required/></td>
            </tr>
            <tr>
                <td>Direccion</td> <td><input type="text" name="direccion" id="direccion" value="<?php echo $direccionDefault; ?>" required/></td>
            </tr>
            <tr>
                <td>Telefono</td> <td><input type="number" name="telefono" id="telefono" value="<?php echo $telefonoDefault; ?>" required/></td>
                <td><input type="submit" value="Guardar"> </td>
            </tr>
        </table>
    </form>
</div>
<div>
    <table cellspacing="5" cellpadding="10">
        <tr style="background-color:#333; color:white;">
            <th>Clave</th><th>Nombre</th><th>Direccion</th><th>Telefono</th><th>Editar</th><th>Borrar</th>
        </tr>
        <?php
        $user = "postgres";
        $password = "pass";
        $url = "pgsql:host=172.17.0.3;port=5432;dbname=mydb";
        $pdo = new PDO($url, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $query = "SELECT * FROM mytable";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr style='background-color:#ccc;'>";
            echo "  <td>"; echo $fila['clave']; echo "</td>";
            echo "  <td>"; echo $fila['nombre']; echo "</td>";
            echo "  <td>"; echo $fila['direccion']; echo "</td>";
            echo "  <td>"; echo $fila['telefono']; echo "</td>";
            echo "  <td> <a href=\"{$_SERVER['PHP_SELF']}?clave={$fila['clave']}\" onclick=\"mostrarCancelar()\" > Editar </a></td>";
            echo "  <td> <a href='#' onclick=\"confirmarBorrar('{$fila['clave']}')\"> Borrar </a></td></tr>";

        }
        ?>
    </table>
    <script>
        function confirmarBorrar(clave) {
            if (confirm("¿Estás seguro de que deseas borrar este registro?")) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?borrar=" + clave;
            }
        }

    </script>
</div>

</body>
</html>
