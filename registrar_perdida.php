<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $alumno_id = $_POST['alumno_id'];
    $libro_id = $_POST['libro_id'];

    // Actualizar la tabla de libros
    $conexion->query("UPDATE libros SET ejemplares = ejemplares - 1 WHERE id = $libro_id");

    // Cambiar el estado del préstamo a "perdido"
    $conexion->query("UPDATE prestamos SET estado = 'perdido' WHERE alumno_id = $alumno_id AND libro_id = $libro_id AND estado = 'activo'");

    header("Location: index.html?mensaje=Extravío registrado correctamente.");
    exit();
}
?>
