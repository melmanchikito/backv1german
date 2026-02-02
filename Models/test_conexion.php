<?php
 require_once "Conexion.php";
    echo "<h2>Probando conexión a la base de datos...</h2>";

if ($conexion) {
    echo "✅ Conexión exitosa a MySQL<br>";

    $resultado = $conexion->query("SELECT DATABASE() as bd");

    if ($resultado) {
        $fila = $resultado->fetch_assoc();
        echo "📦 Base de datos activa: <strong>" . $fila['bd'] . "</strong>";
    } else {
        echo "❌ No se pudo ejecutar la consulta.";
    }

} else {
    echo "❌ Error en la conexión.";
}
?>